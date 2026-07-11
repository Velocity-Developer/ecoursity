<?php
//props
$props = isset($props) ? $props : [
    'title' => $props['title'] ?? '',
    'body' => $props['body'] ?? '',
    'footer' => $props['footer'] ?? '',
    'url' => $props['url'] ?? '',
];
?>

<div
    x-data
    x-show="$store.EcoursityUiModal.show"
    x-cloak
    x-on:keydown.escape.window="$store.EcoursityUiModal.close()"
    class="ecoursity-ui-modal tw:fixed tw:inset-0 tw:z-9999 tw:flex tw:items-center tw:justify-center tw:p-4"
    style="display: none;"
    x-bind:class="{'tw:opacity-0 tw:transition-opacity tw:duration-300 tw:ease-in-out': !$store.EcoursityUiModal.show, 'tw:opacity-100': $store.EcoursityUiModal.show}">
    <div class="ecoursity-ui-modal-overlay tw:absolute tw:inset-0 tw:bg-black/50" @click="$store.EcoursityUiModal.close()"></div>

    <div
        class="ecoursity-ui-modal-content tw:scale-x-74 tw:duration-10 tw:ease-in-out tw:relative tw:z-10 tw:w-full tw:max-w-5xl tw:overflow-hidden tw:rounded-xl tw:bg-white tw:shadow-2xl"
        x-bind:class="{'tw:scale-x-100': $store.EcoursityUiModal.show}">
        <div x-show="$store.EcoursityUiModal.title" class="ecoursity-ui-modal-header tw:border-b tw:border-slate-200 tw:px-6 tw:py-4">
            <h2 class="ecoursity-ui-modal-title tw:m-0! tw:text-xl tw:font-semibold" x-text="$store.EcoursityUiModal.title">

            </h2>
        </div>
        <div x-show="$store.EcoursityUiModal.body" class="ecoursity-ui-modal-body tw:px-6 tw:py-4">

            <div x-show="!$store.EcoursityUiModal.loading" class="ecoursity-ui-modal-body-content" x-html="$store.EcoursityUiModal.body">
            </div>

        </div>
        <div x-show="$store.EcoursityUiModal.footer" class="ecoursity-ui-modal-footer tw:flex tw:justify-end tw:gap-3 tw:border-t tw:border-slate-200 tw:px-6 tw:py-4" x-html="$store.EcoursityUiModal.footer">

        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('EcoursityUiModal', {
            show: false,
            loading: false,
            title: <?php echo wp_json_encode($props['title']); ?>,
            body: <?php echo wp_json_encode($props['body']); ?>,
            footer: <?php echo wp_json_encode($props['footer']); ?>,
            url: <?php echo wp_json_encode($props['url']); ?>,
            async open(payload = {}) {
                this.close();
                this.title = payload.title ?? null;
                this.body = payload.body ?? null;
                this.footer = payload.footer ?? null;
                this.url = payload.url ?? null;
                this.show = true;

                if (this.url) {
                    await this.loadFromUrl();
                }
            },
            setContent(payload = {}) {
                this.title = payload.title ?? this.title;
                this.body = payload.body ?? this.body;
                this.footer = payload.footer ?? this.footer;
                this.url = payload.url ?? this.url;
            },
            close() {
                this.show = false;
                //clear content
                this.setContent({});
            },
            async loadFromUrl() {
                this.loading = true;

                try {
                    const response = await fetch(this.url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    //if json, parse it
                    if (response.headers.get('Content-Type')?.includes('application/json')) {
                        const data = await response.json();
                        this.body = data.html ?? this.body;
                        this.setContent({
                            body: this.body,
                        });
                        return;
                    }

                } catch (error) {
                    console.error(error);
                    this.body = '<div class="tw:text-sm tw:text-red-600">Gagal memuat konten modal.</div>';
                } finally {
                    this.loading = false;
                }
            },
        });
    });
</script>