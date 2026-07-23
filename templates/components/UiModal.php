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

            <div x-ref="modalBody" x-show="!$store.EcoursityUiModal.loading" class="ecoursity-ui-modal-body-content" x-html="$store.EcoursityUiModal.body">
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
                    return;
                }

                this.refreshBody();
            },
            setContent(payload = {}) {
                this.title = payload.title ?? this.title;
                this.body = payload.body ?? this.body;
                this.footer = payload.footer ?? this.footer;
                this.url = payload.url ?? this.url;
            },
            close() {
                this.destroyWpEditors();
                this.show = false;
                this.setContent({
                    title: null,
                    body: null,
                    footer: null,
                    url: null,
                });
            },
            destroyWpEditors() {
                document.querySelectorAll('.ecoursity-ui-modal-body-content textarea.wp-editor-area').forEach((textarea) => {
                    const editorId = textarea.id;

                    if (!editorId) {
                        return;
                    }

                    if (typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
                        tinymce.get(editorId).remove();
                    }

                    if (window.QTags?.instances?.[editorId]) {
                        delete window.QTags.instances[editorId];
                    }
                });
            },
            initWpEditors(modalBody) {
                if (!modalBody || !window.wp?.editor) {
                    return;
                }

                const dedupeEditorTabs = (editorWrap) => {
                    if (!editorWrap) {
                        return;
                    }

                    const tabGroups = Array.from(editorWrap.querySelectorAll('.wp-editor-tabs'));

                    if (tabGroups.length < 2) {
                        return;
                    }

                    const keepIndex = tabGroups.findIndex((tabGroup) => {
                        const tools = tabGroup.closest('.wp-editor-tools');

                        return !!tools?.querySelector('.wp-media-buttons, .insert-media');
                    });
                    const tabGroupToKeep = tabGroups[keepIndex >= 0 ? keepIndex : tabGroups.length - 1];

                    tabGroups.forEach((tabGroup) => {
                        if (tabGroup === tabGroupToKeep) {
                            return;
                        }

                        const tools = tabGroup.closest('.wp-editor-tools');
                        const hasMediaButtons = !!tools?.querySelector('.wp-media-buttons, .insert-media');

                        if (tools && !hasMediaButtons) {
                            tools.remove();
                            return;
                        }

                        tabGroup.remove();
                    });
                };

                const textareas = Array.from(modalBody.querySelectorAll('textarea.wp-editor-area'));

                textareas.forEach((textarea) => {
                    const editorId = textarea.id;

                    if (!editorId) {
                        return;
                    }

                    const editorWrap = modalBody.querySelector(`#wp-${editorId}-wrap`);
                    const hasTinyMce = typeof tinymce !== 'undefined' && !!tinymce.get(editorId);
                    const hasQuicktagsToolbar = !!editorWrap?.querySelector(`#qt_${editorId}_toolbar`);

                    dedupeEditorTabs(editorWrap);

                    if (hasTinyMce && hasQuicktagsToolbar) {
                        return;
                    }

                    if (hasTinyMce) {
                        tinymce.get(editorId).remove();
                    }

                    if (window.QTags?.instances?.[editorId]) {
                        const quicktagsWrapper = document.getElementById(`qt_${editorId}_toolbar`);

                        if (quicktagsWrapper) {
                            quicktagsWrapper.remove();
                        }

                        delete window.QTags.instances[editorId];
                    }

                    const settings = window.tinyMCEPreInit?.mceInit?.[editorId]
                        ? {
                            tinymce: window.tinyMCEPreInit.mceInit[editorId],
                            quicktags: window.tinyMCEPreInit.qtInit?.[editorId] ?? true,
                            mediaButtons: true,
                        }
                        : {
                            ...window.wp.editor.getDefaultSettings(),
                            mediaButtons: true,
                            quicktags: true,
                        };
                    const initialContent = textarea.value;

                    window.wp.editor.initialize(editorId, settings);
                    dedupeEditorTabs(editorWrap);

                    if (typeof tinymce !== 'undefined') {
                        const editor = tinymce.get(editorId);

                        if (editor) {
                            const setInitialContent = () => {
                                editor.setContent(initialContent || '');
                            };

                            if (editor.initialized) {
                                setInitialContent();
                            } else {
                                editor.on('init', setInitialContent);
                            }
                        }
                    }
                });
            },
            refreshBody() {
                const refresh = () => {
                    const modalBody = document.querySelector('.ecoursity-ui-modal-body-content');

                    if (!modalBody) {
                        return;
                    }

                    const scripts = Array.from(modalBody.querySelectorAll('script'));

                    scripts.forEach((script) => {
                        const executableScript = document.createElement('script');

                        Array.from(script.attributes).forEach((attribute) => {
                            executableScript.setAttribute(attribute.name, attribute.value);
                        });

                        executableScript.textContent = script.textContent;
                        script.replaceWith(executableScript);
                    });

                    if (window.Alpine?.initTree) {
                        window.Alpine.initTree(modalBody);
                    }

                    this.initWpEditors(modalBody);
                };

                if (window.Alpine?.nextTick) {
                    window.Alpine.nextTick(refresh);
                    return;
                }

                requestAnimationFrame(refresh);
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
                        this.refreshBody();
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
