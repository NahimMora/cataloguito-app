import Alpine from "alpinejs";
const csrf = () => document.querySelector('meta[name="csrf-token"]').content;
async function api(url, data, method = "POST") {
    const response = await fetch(url, {
        method,
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": csrf(),
        },
        body: JSON.stringify(data),
    });
    const json = await response.json().catch(() => ({}));
    if (!response.ok)
        throw new Error(
            response.status === 419
                ? "Tu sesión venció. Recargá la página."
                : response.status === 429
                  ? "Esperá un momento antes de volver a intentar."
                  : Object.values(json.errors || {})
                        .flat()
                        .join(" ") ||
                    "No pudimos completar la acción. Volvé a intentar.",
        );
    return json;
}
window.money = (minor) =>
    new Intl.NumberFormat("es-AR", {
        style: "currency",
        currency: "ARS",
        maximumFractionDigits: minor % 100 ? 2 : 0,
    }).format(minor / 100);
window.copyText = async (text) => {
    try {
        if (navigator.clipboard?.writeText)
            await navigator.clipboard.writeText(text);
        else {
            const f = document.createElement("textarea");
            f.value = text;
            document.body.appendChild(f);
            f.select();
            document.execCommand("copy");
            f.remove();
        }
        window.dispatchEvent(
            new CustomEvent("notice", {
                detail: "Copiado. Listo para compartir.",
            }),
        );
    } catch {
        window.dispatchEvent(
            new CustomEvent("notice", {
                detail: "Seleccioná el enlace y copialo manualmente.",
            }),
        );
    }
};
Alpine.data("quickPrice", (url, price, version) => ({
    url,
    price,
    version,
    saving: false,
    status: "",
    failed: false,
    async save() {
        this.saving = true;
        this.failed = false;
        this.status = "Guardando…";
        try {
            const r = await api(
                this.url,
                { price: this.price, version: this.version },
                "PATCH",
            );
            this.version = r.version;
            this.price = r.price;
            this.status = "Precio guardado";
        } catch (e) {
            this.failed = true;
            this.status = e.message;
        } finally {
            this.saving = false;
        }
    },
}));
Alpine.data("imagePreview", (current) => ({
    url: current,
    temporary: null,
    select(e) {
        if (this.temporary) URL.revokeObjectURL(this.temporary);
        const f = e.target.files[0];
        if (f) {
            this.temporary = URL.createObjectURL(f);
            this.url = this.temporary;
        }
    },
}));
Alpine.data("catalogCart", (slug, preview = false) => ({
    slug,
    preview,
    items: [],
    opened: false,
    step: "cart",
    busy: false,
    error: "",
    quote: null,
    name: "",
    phone: "",
    note: "",
    website: "",
    key: "",
    returnFocus: null,
    init() {
        this.key = crypto.randomUUID();
        try {
            const s = JSON.parse(localStorage.getItem("vitrina-cart-" + slug));
            if (s && s.expires > Date.now() && Array.isArray(s.items))
                this.items = s.items
                    .filter(
                        (i) =>
                            /^[A-Za-z0-9]{26}$/.test(i.id) &&
                            Number.isInteger(i.quantity) &&
                            i.quantity > 0 &&
                            i.quantity <= 999 &&
                            typeof i.name === "string" &&
                            Number.isSafeInteger(i.price),
                    )
                    .slice(0, 50);
        } catch {}
        this.$watch("items", () => {
            try {
                localStorage.setItem(
                    "vitrina-cart-" + slug,
                    JSON.stringify({
                        items: this.items,
                        expires: Date.now() + 7 * 86400000,
                    }),
                );
            } catch {}
        });
    },
    get count() {
        return this.items.reduce((s, i) => s + i.quantity, 0);
    },
    get total() {
        return this.items.reduce((s, i) => s + i.quantity * i.price, 0);
    },
    add(p) {
        if (this.preview) {
            this.$dispatch(
                "notice",
                "Los pedidos se realizan desde el catálogo publicado.",
            );
            return;
        }
        const i = this.items.find((i) => i.id === p.id);
        if (i) {
            if (i.quantity < 999) i.quantity++;
        } else {
            if (this.items.length >= 50) {
                this.$dispatch(
                    "notice",
                    "Hasta 50 productos diferentes por pedido.",
                );
                return;
            }
            this.items.push({ ...p, quantity: 1 });
        }
        this.changed();
        this.$dispatch("notice", p.name + " agregado al pedido");
    },
    quantity(id, d) {
        const i = this.items.find((i) => i.id === id);
        if (!i) return;
        i.quantity = Math.min(999, i.quantity + d);
        this.items = this.items.filter((i) => i.quantity > 0);
        this.changed();
    },
    changed() {
        this.quote = null;
        this.step = "cart";
        this.error = "";
        this.key = crypto.randomUUID();
    },
    open() {
        this.returnFocus = document.activeElement;
        this.opened = true;
        document.body.style.overflow = "hidden";
        this.$nextTick(() => this.$refs.closeCart.focus());
    },
    close() {
        if (this.busy) return;
        this.opened = false;
        document.body.style.overflow = "";
        this.returnFocus?.focus();
    },
    trap(e) {
        if (e.key !== "Tab") return;
        const n = [
            ...this.$refs.drawer.querySelectorAll(
                "button:not([disabled]),a[href],input:not([type=hidden]),textarea",
            ),
        ].filter((e) => e.offsetParent !== null);
        if (!n.length) return;
        const f = n[0],
            l = n.at(-1);
        if (e.shiftKey && document.activeElement === f) {
            e.preventDefault();
            l.focus();
        } else if (!e.shiftKey && document.activeElement === l) {
            e.preventDefault();
            f.focus();
        }
    },
    async review() {
        if (!this.items.length || this.preview) return;
        this.busy = true;
        this.error = "";
        try {
            this.quote = await api("/" + this.slug + "/quote", {
                items: this.items.map((i) => ({
                    id: i.id,
                    quantity: i.quantity,
                })),
            });
            this.items = this.items.map((i) => {
                const l = this.quote.lines.find((l) => l.id === i.id);
                return { ...i, name: l.name, price: l.unit_price_minor };
            });
            this.step = "checkout";
            this.$nextTick(() => this.$refs.buyerName.focus());
        } catch (e) {
            this.error = e.message;
        } finally {
            this.busy = false;
        }
    },
    async confirm() {
        if (this.busy || !this.quote) return;
        this.busy = true;
        this.error = "";
        try {
            const r = await api("/" + this.slug + "/orders", {
                items: this.items.map((i) => ({
                    id: i.id,
                    quantity: i.quantity,
                })),
                quote: this.quote.token,
                name: this.name,
                phone: this.phone,
                note: this.note,
                website: this.website,
                key: this.key,
            });
            this.items = [];
            try {
                localStorage.removeItem("vitrina-cart-" + this.slug);
            } catch {}
            window.location.assign(r.url);
        } catch (e) {
            this.error = e.message;
        } finally {
            this.busy = false;
        }
    },
}));
window.Alpine = Alpine;
Alpine.start();
