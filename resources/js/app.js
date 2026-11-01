import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

const formatBytes = (bytes) => {
    if (!Number.isFinite(bytes) || bytes <= 0) {
        return '0 KB';
    }

    const units = ['B', 'KB', 'MB', 'GB'];
    const index = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);

    return `${(bytes / Math.pow(1024, index)).toFixed(index === 0 ? 0 : 1)} ${units[index]}`;
};

document.querySelectorAll('[data-image-preview]').forEach((input) => {
    const target = document.querySelector(input.dataset.imagePreviewTarget);

    if (!target) {
        return;
    }

    const multiple = input.multiple;
    const maxFiles = Number.parseInt(input.dataset.maxFiles || (multiple ? '8' : '1'), 10);
    const primaryName = input.dataset.primaryName || null;
    let selectedFiles = [];
    let primaryIndex = 0;
    let objectUrls = [];

    const revokeObjectUrls = () => {
        objectUrls.forEach((url) => URL.revokeObjectURL(url));
        objectUrls = [];
    };

    const syncInput = () => {
        if (!multiple) {
            return;
        }

        const transfer = new DataTransfer();
        selectedFiles.forEach((file) => transfer.items.add(file));
        input.files = transfer.files;
    };

    const render = () => {
        revokeObjectUrls();
        target.replaceChildren();

        if (selectedFiles.length === 0) {
            return;
        }

        selectedFiles.forEach((file, index) => {
            const item = document.createElement('div');
            item.className = 'admin-image-preview-item';

            const image = document.createElement('img');
            const objectUrl = URL.createObjectURL(file);
            objectUrls.push(objectUrl);
            image.src = objectUrl;
            image.alt = file.name;
            image.loading = 'lazy';

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'btn btn-sm btn-light admin-image-preview-remove';
            remove.innerHTML = '<i class="bi bi-x-lg"></i>';
            remove.setAttribute('aria-label', `Remove ${file.name}`);
            remove.addEventListener('click', () => {
                selectedFiles.splice(index, 1);

                if (primaryIndex >= selectedFiles.length) {
                    primaryIndex = Math.max(0, selectedFiles.length - 1);
                }

                syncInput();
                render();
            });

            const meta = document.createElement('div');
            meta.className = 'admin-image-preview-meta';

            const name = document.createElement('span');
            name.className = 'admin-image-preview-name';
            name.textContent = file.name;

            const size = document.createElement('span');
            size.className = 'admin-image-preview-size';
            size.textContent = formatBytes(file.size);

            meta.append(name, size);

            if (primaryName) {
                const primary = document.createElement('label');
                primary.className = 'admin-image-preview-primary';

                const radio = document.createElement('input');
                radio.type = 'radio';
                radio.name = primaryName;
                radio.value = String(index);
                radio.className = 'form-check-input';
                radio.checked = index === primaryIndex;
                radio.addEventListener('change', () => {
                    primaryIndex = index;
                });

                const label = document.createElement('span');
                label.textContent = 'Primary';

                primary.append(radio, label);
                meta.append(primary);
            }

            item.append(image, remove, meta);
            target.append(item);
        });
    };

    input.addEventListener('change', () => {
        const incoming = Array.from(input.files || []);

        if (!multiple) {
            selectedFiles = incoming.slice(0, 1);
            primaryIndex = 0;
            syncInput();
            render();

            return;
        }

        incoming.forEach((file) => {
            const duplicate = selectedFiles.some(
                (selected) =>
                    selected.name === file.name &&
                    selected.size === file.size &&
                    selected.lastModified === file.lastModified,
            );

            if (!duplicate) {
                selectedFiles.push(file);
            }
        });

        if (selectedFiles.length > maxFiles) {
            selectedFiles = selectedFiles.slice(0, maxFiles);
        }

        if (primaryIndex >= selectedFiles.length) {
            primaryIndex = Math.max(0, selectedFiles.length - 1);
        }

        syncInput();
        render();
    });
});

document.addEventListener(
    'error',
    (event) => {
        const image = event.target;

        if (!(image instanceof HTMLImageElement) || !image.dataset.fallback) {
            return;
        }

        if (image.src === image.dataset.fallback) {
            return;
        }

        image.src = image.dataset.fallback;
    },
    true,
);
