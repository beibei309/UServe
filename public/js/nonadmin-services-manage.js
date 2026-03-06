(() => {
    const config = document.getElementById('servicesManageConfig');
    if (!config) return;

    const editUrlTemplate = config.dataset.editUrlTemplate || '';
    const deleteUrlTemplate = config.dataset.deleteUrlTemplate || '';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    function openServiceModal(service) {
        const modal = document.getElementById('serviceModal');
        if (!modal) return;

        const serviceId = service.hss_id ?? service.id;
        const getField = (prefixed, fallback) => service[prefixed] ?? service[fallback] ?? null;
        const imageUrl = service.ui_image_url || 'https://via.placeholder.com/400x300?text=Service+Image';

        document.getElementById('modalTitle').textContent = getField('hss_title', 'title') || 'Untitled Service';
        document.getElementById('modalImage').src = imageUrl;

        const categoryEl = document.getElementById('modalCategory');
        if (service.category) {
            categoryEl.textContent = service.category.hc_name ?? service.category.name ?? '';
        } else {
            categoryEl.textContent = '';
        }

        const descContent = document.getElementById(`data-desc-${serviceId}`)?.innerHTML || '';
        document.getElementById('modalDescription').innerHTML = descContent;

        const pkgContainer = document.getElementById('modalPackagesContainer');
        pkgContainer.innerHTML = '';

        const tiers = [
            { key: 'basic', label: 'Basic Tier', color: 'teal', badge: 'bg-teal-100 text-teal-700' },
            { key: 'standard', label: 'Standard Tier', color: 'yellow', badge: 'bg-yellow-100 text-yellow-700' },
            { key: 'premium', label: 'Premium Tier', color: 'red', badge: 'bg-red-100 text-red-700' },
        ];
        const colors = {
            teal: 'bg-white border-teal-600 hover:border-teal-600',
            yellow: 'bg-yellow-50/50 border-yellow-600 hover:border-yellow-600',
            red: 'bg-red-50/50 border-red-600 hover:border-red-600',
        };

        let hasPackages = false;
        tiers.forEach((tier) => {
            const price = getField(`hss_${tier.key}_price`, `${tier.key}_price`);
            const frequency = getField(`hss_${tier.key}_frequency`, `${tier.key}_frequency`);
            if (!price) return;

            hasPackages = true;
            const desc = document.getElementById(`data-pkg-${tier.key}-desc-${serviceId}`)?.innerHTML || '';
            pkgContainer.innerHTML += `
                <div class="relative flex flex-col md:flex-row gap-5 border rounded-2xl p-5 ${colors[tier.color]} transition hover:shadow-md group">
                    <div class="md:w-1/3 flex flex-col justify-center border-b md:border-b-0 md:border-r border-black/5 pb-4 md:pb-0 md:pr-5">
                        <span class="inline-block self-start px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider mb-2 ${tier.badge}">${tier.label}</span>
                        <div class="flex items-baseline gap-1">
                            <span class="text-2xl font-extrabold text-gray-900">RM${price}</span>
                        </div>
                        <span class="text-xs text-gray-500 font-medium">per ${frequency || 'session'}</span>
                    </div>
                    <div class="md:w-2/3 prose prose-sm max-w-none text-gray-600 text-sm flex items-center">
                        <div>${desc || '<span class="italic opacity-50">No description provided.</span>'}</div>
                    </div>
                </div>
            `;
        });

        if (!hasPackages) {
            pkgContainer.innerHTML =
                '<div class="text-center p-6 text-gray-400 italic bg-gray-50 rounded-xl border border-dashed border-gray-200">No pricing packages configured.</div>';
        }

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeServiceModal() {
        const modal = document.getElementById('serviceModal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function editService(id) {
        window.location.href = editUrlTemplate.replace('__ID__', id);
    }

    function deleteService(serviceId) {
        Swal.fire({
            title: 'Delete Service?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it',
        }).then((result) => {
            if (!result.isConfirmed) return;
            const url = deleteUrlTemplate.replace('__ID__', serviceId);
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                },
            })
                .then((res) => res.json())
                .then((data) => {
                    if (!data.service) {
                        Swal.fire('Error', 'Unable to delete.', 'error');
                        return;
                    }
                    document.querySelector(`[data-service-id='${serviceId}']`)?.remove();
                    Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    }).fire({
                        icon: 'success',
                        title: 'Service deleted successfully',
                    });
                });
        });
    }

    tabButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.tab;
            tabContents.forEach((tc) => tc.classList.add('hidden'));
            document.getElementById(`${target}-tab-content`)?.classList.remove('hidden');

            tabButtons.forEach((button) => {
                button.classList.remove('border-indigo-600', 'text-indigo-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            btn.classList.remove('border-transparent', 'text-gray-500');
            btn.classList.add('border-indigo-600', 'text-indigo-600');
        });
    });

    document.querySelectorAll('img[data-fallback-src]').forEach((img) => {
        img.addEventListener('error', () => {
            if (img.dataset.fallbackApplied === '1') return;
            img.dataset.fallbackApplied = '1';
            img.src = img.dataset.fallbackSrc;
        });
    });

    document.addEventListener('click', (event) => {
        const editBtn = event.target.closest('[data-edit-service]');
        if (editBtn) {
            editService(editBtn.dataset.editService);
            return;
        }

        const openModalBtn = event.target.closest('[data-open-service-modal]');
        if (openModalBtn) {
            openServiceModal(JSON.parse(openModalBtn.dataset.openServiceModal));
            return;
        }

        const deleteBtn = event.target.closest('[data-delete-service]');
        if (deleteBtn) {
            deleteService(deleteBtn.dataset.deleteService);
            return;
        }

        if (event.target.closest('[data-close-service-modal]')) {
            closeServiceModal();
        }
    });
})();
