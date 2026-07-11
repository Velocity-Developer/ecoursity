<?php
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
    class="ecoursity-ui-modal"
    style="display: none;">
    <div class="ecoursity-ui-modal-overlay" @click="$store.EcoursityUiModal.close()"></div>

    <div
        class="ecoursity-ui-modal-content"
        x-bind:class="{'ecoursity-ui-modal-content--open': $store.EcoursityUiModal.show}">
        <div x-show="$store.EcoursityUiModal.title" class="ecoursity-ui-modal-header">
            <h2 class="ecoursity-ui-modal-title" x-text="$store.EcoursityUiModal.title">

            </h2>
        </div>
        <div x-show="$store.EcoursityUiModal.body" class="ecoursity-ui-modal-body">

            <div x-show="!$store.EcoursityUiModal.loading" class="ecoursity-ui-modal-body-content" x-html="$store.EcoursityUiModal.body">
            </div>

        </div>
        <div x-show="$store.EcoursityUiModal.footer" class="ecoursity-ui-modal-footer" x-html="$store.EcoursityUiModal.footer">

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
                    this.body = '<div class="ecoursity-modal-error">Gagal memuat konten modal.</div>';
                } finally {
                    this.loading = false;
                }
            },
        });
    });
</script>