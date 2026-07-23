<?php

use Ecoursity\App\Models\File as FileModel;

$post_id = absint($props['post_id'] ?? $props['item_id'] ?? $props['id'] ?? 0);
$post_type = sanitize_key((string) ($props['item_type'] ?? ''));

if ($post_type === '' && $post_id > 0) {
    $post_type = sanitize_key((string) get_post_type($post_id));
}

$rest_url = get_rest_url(null, 'ecoursity/v1/files/');
$files = $post_id > 0 ? FileModel::allByItem($post_id, $post_type) : [];
$file_payload = array_map(
    static fn(FileModel $file): array => $file->toArray(),
    $files
);
?>

<div
    class="ecoursity-file-form"
    x-data="ecoursityFileForm(
        <?php echo (int) $post_id; ?>,
        '<?php echo esc_js($post_type); ?>',
        '<?php echo esc_js($rest_url); ?>',
        <?php echo esc_attr(wp_json_encode($file_payload)); ?>
    )"
    x-cloak>
    <form class="ecoursity-file-form__create" @submit.prevent="submit">
        <div x-show="message" class="ecoursity-form-message" :class="'ecoursity-form-message--' + messageType" x-text="message"></div>

        <div class="ecoursity-file-form__grid">
            <div class="ecoursity-form-group">
                <label class="ecoursity-form-label">Judul File</label>
                <input type="text" class="ecoursity-form-input" x-model="form.file_name" placeholder="Nama lampiran">
            </div>

            <div class="ecoursity-form-group">
                <label class="ecoursity-form-label">Method</label>
                <select class="ecoursity-form-select" x-model="form.method">
                    <option value="upload">Upload</option>
                    <option value="external">External</option>
                </select>
            </div>
        </div>

        <div class="ecoursity-form-group" x-show="form.method === 'upload'">
            <label class="ecoursity-form-label">Upload File</label>
            <input type="file" class="ecoursity-form-input" x-ref="fileInput">
        </div>

        <div class="ecoursity-form-group" x-show="form.method === 'external'">
            <label class="ecoursity-form-label">URL File</label>
            <input type="url" class="ecoursity-form-input" x-model="form.file_path" placeholder="https://example.com/file.pdf">
        </div>

        <div class="ecoursity-form-actions ecoursity-file-form__actions">
            <button type="submit" class="ecoursity-button ecoursity-button--primary" :disabled="saving || !itemId" x-text="saving ? 'Menyimpan...' : 'Tambah File'"></button>
        </div>
    </form>

    <template x-if="!itemId">
        <div class="ecoursity-file-form__empty">Simpan post dulu sebelum menambahkan file.</div>
    </template>

    <template x-if="itemId && !files.length">
        <div class="ecoursity-file-form__empty">Belum ada file lampiran.</div>
    </template>

    <div class="ecoursity-file-form__accordion" x-show="files.length">
        <template x-for="file in files" :key="file.file_id">
            <details class="ecoursity-file-form__item">
                <summary class="ecoursity-file-form__summary">
                    <span class="ecoursity-file-form__title" x-text="file.file_name || 'File lampiran'"></span>
                    <span class="ecoursity-file-form__method" x-text="formatMethod(file.method)"></span>
                </summary>

                <div class="ecoursity-file-form__detail">
                    <div class="ecoursity-file-form__row">
                        <span>Title</span>
                        <strong x-text="file.file_name || '-'"></strong>
                    </div>
                    <div class="ecoursity-file-form__row">
                        <span>Method</span>
                        <strong x-text="formatMethod(file.method)"></strong>
                    </div>
                    <div class="ecoursity-file-form__row">
                        <span>Action</span>
                        <span class="ecoursity-file-form__row-actions">
                            <a class="ecoursity-file-form__icon-action" :href="file.url || file.file_path" target="_blank" rel="noopener noreferrer" x-show="file.url || file.file_path" aria-label="Buka file" title="Buka file">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16" aria-hidden="true">
                                    <path d="M6.667 3.333H3.333A1.333 1.333 0 0 0 2 4.667v8A1.333 1.333 0 0 0 3.333 14h8a1.333 1.333 0 0 0 1.334-1.333V9.333" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10 2h4v4" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M6.667 9.333 14 2" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <button type="button" class="ecoursity-file-form__icon-action ecoursity-file-form__icon-action--danger" @click="deleteFile(file)" aria-label="Hapus file" title="Hapus file">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16" aria-hidden="true">
                                    <path d="M2.667 4h10.666" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M6.667 7.333v4M9.333 7.333v4" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="m3.333 4 .667 8c.056.75.681 1.333 1.433 1.333h5.134c.752 0 1.377-.583 1.433-1.333l.667-8" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M6 4V2.667C6 2.3 6.3 2 6.667 2h2.666C9.7 2 10 2.3 10 2.667V4" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </span>
                    </div>
                </div>
            </details>
        </template>
    </div>
</div>

<style>
    .ecoursity-file-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
        max-width: 840px;
        font-family: "Forma DJR Micro", Manrope, Inter, Roboto, Arial, sans-serif;
    }

    .ecoursity-file-form__create {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .ecoursity-file-form__grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 16px;
    }

    .ecoursity-file-form__actions {
        border-top: 0;
        padding-top: 0;
    }

    .ecoursity-file-form__empty {
        padding: 16px;
        border: 1px dashed #c2c2c2;
        border-radius: 6px;
        background: #f7f7f7;
        color: #636363;
        font-size: 14px;
        line-height: 1.5;
    }

    .ecoursity-file-form__accordion {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .ecoursity-file-form__item {
        border: 1px solid #e8e8e8;
        border-radius: 6px;
        background: #ffffff;
        overflow: hidden;
    }

    .ecoursity-file-form__summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        cursor: pointer;
        list-style: none;
    }

    .ecoursity-file-form__summary::-webkit-details-marker {
        display: none;
    }

    .ecoursity-file-form__title {
        min-width: 0;
        color: #1a1a1a;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .ecoursity-file-form__method {
        flex: 0 0 auto;
        padding: 4px 8px;
        border-radius: 999px;
        background: #eef4ff;
        color: #024ad8;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.3;
        text-transform: capitalize;
    }

    .ecoursity-file-form__detail {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 0 16px 16px;
        border-top: 1px solid #e8e8e8;
    }

    .ecoursity-file-form__row {
        display: grid;
        grid-template-columns: 96px minmax(0, 1fr);
        gap: 12px;
        align-items: center;
        font-size: 14px;
        line-height: 1.5;
        color: #636363;
    }

    .ecoursity-file-form__row strong {
        color: #1a1a1a;
        overflow-wrap: anywhere;
    }

    .ecoursity-file-form__row-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .ecoursity-file-form__icon-action {
        display: inline-flex;
        width: 32px;
        height: 32px;
        align-items: center;
        justify-content: center;
        border: 1px solid #c2c2c2;
        border-radius: 4px;
        background: #ffffff;
        color: #1a1a1a;
        cursor: pointer;
        text-decoration: none;
    }

    .ecoursity-file-form__icon-action--danger {
        color: #b3262b;
        border-color: #efb4b4;
    }

    @media (min-width: 720px) {
        .ecoursity-file-form__grid {
            grid-template-columns: minmax(0, 1fr) 180px;
        }
    }
</style>

<script>
    (() => {
        const registerFileForm = () => {
            if (!window.Alpine || window.__ecoursityFileFormRegistered) {
                return;
            }

            window.__ecoursityFileFormRegistered = true;

            window.Alpine.data('ecoursityFileForm', (itemId, itemType, restUrl, defaults) => ({
                itemId: parseInt(itemId, 10) || 0,
                itemType,
                restUrl,
                files: Array.isArray(defaults) ? defaults : [],
                saving: false,
                message: '',
                messageType: 'success',
                form: {
                    file_name: '',
                    method: 'upload',
                    file_path: '',
                },
                formatMethod(method) {
                    return method === 'external' ? 'External' : 'Upload';
                },
                headers() {
                    const headers = {
                        'X-Requested-With': 'XMLHttpRequest',
                    };

                    if (window.ecoursity?.restNonce) {
                        headers['X-WP-Nonce'] = window.ecoursity.restNonce;
                    }

                    return headers;
                },
                async refresh() {
                    if (!this.itemId) {
                        return;
                    }

                    const params = new URLSearchParams({
                        item_id: this.itemId,
                        item_type: this.itemType,
                    });
                    const response = await fetch(`${this.restUrl}?${params.toString()}`, {
                        headers: this.headers(),
                    });
                    const json = await response.json();

                    if (json.success && Array.isArray(json.data)) {
                        this.files = json.data;
                    }
                },
                async submit() {
                    if (!this.itemId) {
                        return;
                    }

                    this.saving = true;
                    this.message = '';

                    const body = new FormData();
                    body.append('item_id', this.itemId);
                    body.append('item_type', this.itemType);
                    body.append('method', this.form.method);
                    body.append('file_name', this.form.file_name);
                    body.append('orders', this.files.length);

                    if (this.form.method === 'external') {
                        body.append('file_path', this.form.file_path);
                    } else if (this.$refs.fileInput?.files?.[0]) {
                        body.append('file', this.$refs.fileInput.files[0]);
                    }

                    try {
                        const response = await fetch(this.restUrl, {
                            method: 'POST',
                            headers: this.headers(),
                            body,
                        });
                        const json = await response.json();

                        if (!json.success) {
                            this.message = json.message || 'Gagal menyimpan file.';
                            this.messageType = 'error';
                            return;
                        }

                        this.message = json.message || 'File berhasil disimpan.';
                        this.messageType = 'success';
                        this.form.file_name = '';
                        this.form.file_path = '';

                        if (this.$refs.fileInput) {
                            this.$refs.fileInput.value = '';
                        }

                        await this.refresh();
                    } catch (error) {
                        this.message = 'Gagal menyimpan file.';
                        this.messageType = 'error';
                    } finally {
                        this.saving = false;
                    }
                },
                async deleteFile(file) {
                    if (!file?.file_id || !confirm('Hapus file ini?')) {
                        return;
                    }

                    const response = await fetch(`${this.restUrl}${file.file_id}`, {
                        method: 'DELETE',
                        headers: this.headers(),
                    });
                    const json = await response.json();

                    if (json.success) {
                        this.files = this.files.filter((item) => item.file_id !== file.file_id);
                        this.message = json.message || 'File berhasil dihapus.';
                        this.messageType = 'success';
                        return;
                    }

                    this.message = json.message || 'Gagal menghapus file.';
                    this.messageType = 'error';
                },
            }));
        };

        registerFileForm();
        document.addEventListener('alpine:init', registerFileForm);
    })();
</script>
