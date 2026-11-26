{{-- Registered in the panel <head> (not @once in the field view): scripts inside Livewire update responses don't run,
     so pickers that appear later ("Add day", the create page) would otherwise never find their component. --}}
<style>
    .sun-picker-pin, .sun-picker-other { background: none; border: 0; }
    .sun-picker-pin span {
        display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; transform: translate(-50%, -100%);
        border-radius: 50% 50% 50% 0; rotate: -45deg; background: #d9600f; box-shadow: 0 0 0 3px #fff, 0 2px 6px rgb(0 0 0 / .35);
    }
    .sun-picker-pin b { rotate: 45deg; color: #fff; font: 700 12px/1 ui-sans-serif, system-ui, sans-serif; }
    .sun-picker-other span {
        display: flex; align-items: center; justify-content: center; width: 18px; height: 18px; transform: translate(-50%, -50%);
        border-radius: 9999px; background: #6b7280; color: #fff; font: 700 10px/1 ui-sans-serif, system-ui, sans-serif; box-shadow: 0 0 0 2px #fff;
    }
</style>
<script>
    (() => {
        const leafletBase = @js(asset('vendor/leaflet'));
        let leaflet = null;

        // Leaflet is self-hosted and only fetched the first time a picker is shown.
        const loadLeaflet = () => leaflet ??= new Promise((resolve, reject) => {
            if (window.L) return resolve(window.L);
            const css = document.createElement('link');
            css.rel = 'stylesheet';
            css.href = `${leafletBase}/leaflet.css`;
            document.head.append(css);
            const script = document.createElement('script');
            script.src = `${leafletBase}/leaflet.js`;
            script.onload = () => resolve(window.L);
            script.onerror = reject;
            document.head.append(script);
        });

        const icon = (L, className, text) => {
            const outer = document.createElement('span');
            const inner = document.createElement('b');
            inner.textContent = text ?? '';
            if (className === 'sun-picker-pin') outer.append(inner); else outer.textContent = text ?? '';
            return L.divIcon({ className, html: outer, iconSize: [0, 0], iconAnchor: [0, 0] });
        };

        window.sunMapPicker = (config) => ({
            map: null,
            observer: null,

            async init() {
                const L = await loadLeaflet();
                const element = this.$refs.map;
                if (!element || !element.isConnected) return;

                const views = (window.__sunMapPickerViews ??= {});
                const map = L.map(element, { scrollWheelZoom: false });
                this.map = map;
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 18,
                }).addTo(map);

                for (const other of config.others) {
                    L.marker([other.lat, other.lng], { icon: icon(L, 'sun-picker-other', String(other.day)), interactive: false }).addTo(map);
                }

                let marker = null;
                const placeMarker = (latlng) => {
                    if (marker) return marker.setLatLng(latlng);
                    marker = L.marker(latlng, { draggable: true, icon: icon(L, 'sun-picker-pin', config.day ? String(config.day) : '') }).addTo(map);
                    marker.on('dragend', () => this.save(marker.getLatLng()));
                };
                if (config.point) placeMarker([config.point.lat, config.point.lng]);

                map.on('click', (event) => {
                    placeMarker(event.latlng);
                    this.save(event.latlng);
                });
                map.on('moveend', () => {
                    views[config.itemPath] = { center: map.getCenter(), zoom: map.getZoom() };
                });

                const applyView = () => {
                    const saved = views[config.itemPath];
                    if (saved) return map.setView(saved.center, saved.zoom, { animate: false });
                    if (config.point) return map.setView([config.point.lat, config.point.lng], 8, { animate: false });
                    if (config.others.length > 1) return map.fitBounds(config.others.map((o) => [o.lat, o.lng]), { padding: [30, 30], maxZoom: 9 });
                    if (config.others.length === 1) return map.setView([config.others[0].lat, config.others[0].lng], 8, { animate: false });
                    map.setView([-6.2, 35.6], 5, { animate: false });
                };
                applyView();

                // Days start collapsed, so the map may be 0px wide at first: resize and re-centre once it becomes visible.
                let wasHidden = element.offsetWidth === 0;
                this.observer = new ResizeObserver(() => {
                    if (!element.isConnected || !this.map) return;
                    if (element.offsetWidth === 0) {
                        wasHidden = true;
                        return;
                    }
                    map.invalidateSize();
                    if (wasHidden) {
                        wasHidden = false;
                        applyView();
                    }
                });
                this.observer.observe(element);
            },

            save(latlng) {
                const round = (value) => Math.round(value * 1e5) / 1e5;
                this.$wire.$set(`${config.itemPath}.map_lat`, round(latlng.lat), false);
                this.$wire.$set(`${config.itemPath}.map_lng`, round(latlng.lng), false);
                this.$wire.$set(`${config.itemPath}.map_place`, 'custom');
            },

            destroy() {
                this.observer?.disconnect();
                this.map?.remove();
                this.map = null;
            },
        });
    })();
</script>
