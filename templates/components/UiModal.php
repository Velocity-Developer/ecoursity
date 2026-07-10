<?php
//props
$props = isset($props) ? $props : [
    'title' => $props['title'] ?? '',
    'body' => $props['body'] ?? '',
    'footer' => $props['footer'] ?? '',
];
?>

<div
    x-data
    x-show="$store.EcoursityUiModal.show"
    x-cloak
    class="ecoursity-ui-modal tw:opacity-0 tw:fixed tw:inset-0 tw:z-200 tw:flex tw:items-center tw:justify-center tw:p-4"
    x-bind:class="{'tw:opacity-100': $store.EcoursityUiModal.show}">
    <div class="ecoursity-ui-modal-overlay tw:absolute tw:inset-0 tw:bg-black/50" @click="$store.EcoursityUiModal.close()"></div>

    <div class="ecoursity-ui-modal-content tw:relative tw:z-10 tw:w-full tw:max-w-2xl tw:rounded-xl tw:bg-white tw:shadow-2xl">
        <div class="ecoursity-ui-modal-header tw:border-b tw:border-slate-200 tw:px-6 tw:py-4">
            <h2 class="ecoursity-ui-modal-title tw:text-xl tw:font-semibold tw:text-slate-900" x-text="$store.EcoursityUiModal.title">

            </h2>
        </div>
        <div class="ecoursity-ui-modal-body tw:px-6 tw:py-4 tw:text-slate-700" x-html="$store.EcoursityUiModal.body">

        </div>
        <div class="ecoursity-ui-modal-footer tw:flex tw:justify-end tw:gap-3 tw:border-t tw:border-slate-200 tw:px-6 tw:py-4" x-html="$store.EcoursityUiModal.footer">

        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('EcoursityUiModal', {
            show: false,
            title: <?php echo wp_json_encode($props['title']); ?>,
            body: <?php echo wp_json_encode($props['body']); ?>,
            footer: <?php echo wp_json_encode($props['footer']); ?>,
            open(payload = {}) {
                this.title = payload.title ?? this.title;
                this.body = payload.body ?? this.body;
                this.footer = payload.footer ?? this.footer;
                this.show = true;
            },
            close() {
                this.show = false;
            },
            setContent(payload = {}) {
                this.title = payload.title ?? this.title;
                this.body = payload.body ?? this.body;
                this.footer = payload.footer ?? this.footer;
            },
        });
    });
</script>