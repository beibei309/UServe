window.UServeAdmin.register('requests', 'adminModuleRequestsConfig', (config) => {
    if (!config) return;

    const warningLimit = parseInt(config.dataset.warningLimit || '3', 10);
    const resolveBaseUrl = config.dataset.resolveBaseUrl || '';

    let currentRequesterId = null;
    let currentProviderId = null;
    let currentRequesterRole = null;
    let currentProviderRole = null;
    let currentRequesterWarnings = 0;
    let currentProviderWarnings = 0;

    window.openViewModal = function (req, service, requester, provider) {
        const reqId = req.hsr_id ?? req.id;
        const serviceTitle = service?.hss_title ?? service?.title ?? 'Unknown Service';
        const selectedPackage = req.hsr_selected_package ?? req.selected_package;
        const packageLabel = Array.isArray(selectedPackage) ? (selectedPackage[0] ?? 'Custom') : (selectedPackage ?? 'Custom');
        const offeredPrice = req.hsr_offered_price ?? req.offered_price ?? 0;
        const status = req.hsr_status ?? req.status ?? 'pending';
        const requesterName = requester?.hu_name ?? requester?.name ?? 'Unknown';
        const providerName = provider?.hu_name ?? provider?.name ?? 'Unknown';
        const requesterEmail = requester?.hu_email ?? requester?.email ?? '-';
        const providerEmail = provider?.hu_email ?? provider?.email ?? '-';
        const requesterPhone = requester?.hu_phone ?? requester?.phone ?? null;
        const providerPhone = provider?.hu_phone ?? provider?.phone ?? null;
        const selectedDates = req.hsr_selected_dates ?? req.selected_dates;
        const dateLabel = Array.isArray(selectedDates) ? (selectedDates[0] ?? null) : selectedDates;
        const startTime = req.hsr_start_time ?? req.start_time;
        const endTime = req.hsr_end_time ?? req.end_time;
        const message = req.hsr_message ?? req.message;
        const disputeReason = req.hsr_dispute_reason ?? req.dispute_reason;

        document.getElementById('viewId').textContent = reqId;
        document.getElementById('viewServiceTitle').textContent = serviceTitle;
        document.getElementById('viewPackage').textContent = packageLabel;
        document.getElementById('viewPrice').textContent = parseFloat(offeredPrice || 0).toFixed(2);

        const statusSpan = document.getElementById('viewStatus');
        statusSpan.textContent = String(status).replace('_', ' ');
        statusSpan.className = 'inline-block mt-1 px-3 py-1 rounded-full text-xs font-bold capitalize border';
        if (status === 'completed') statusSpan.classList.add('bg-green-100', 'text-green-700', 'border-green-200');
        else if (status === 'pending') statusSpan.classList.add('bg-yellow-100', 'text-yellow-700', 'border-yellow-200');
        else if (status === 'disputed') statusSpan.classList.add('bg-red-100', 'text-red-700', 'border-red-200');
        else statusSpan.classList.add('bg-gray-100', 'text-gray-700', 'border-gray-200');

        document.getElementById('viewReqAvatar').textContent = requesterName.charAt(0);
        document.getElementById('viewReqName').textContent = requesterName;
        document.getElementById('viewReqEmail').textContent = requesterEmail;
        document.getElementById('viewReqPhone').textContent = requesterPhone || 'No Phone';

        document.getElementById('viewProvAvatar').textContent = providerName.charAt(0);
        document.getElementById('viewProvName').textContent = providerName;
        document.getElementById('viewProvEmail').textContent = providerEmail;
        document.getElementById('viewProvPhone').textContent = providerPhone || 'No Phone';

        document.getElementById('viewDate').textContent = dateLabel || 'Flexible';
        document.getElementById('viewTime').textContent = `${startTime || '??'} - ${endTime || '??'}`;
        document.getElementById('viewMessage').textContent = message || 'No additional message.';

        const disputeDiv = document.getElementById('viewDisputeSection');
        if (status === 'disputed') {
            disputeDiv.classList.remove('hidden');
            document.getElementById('viewDisputeReason').textContent = disputeReason || 'No reason provided';
        } else {
            disputeDiv.classList.add('hidden');
        }
        document.getElementById('viewDetailModal').classList.remove('hidden');
    };

    window.closeViewModal = function () {
        document.getElementById('viewDetailModal').classList.add('hidden');
    };

    window.openDisciplineModal = function (requestId, reason, requester, provider, reporter) {
        document.getElementById('discModalReason').textContent = reason || 'No reason provided.';
        document.getElementById('discReporterName').textContent = reporter?.name || 'Unknown';
        document.getElementById('discReporterRole').textContent = reporter?.role ? `(${reporter.role})` : '';

        document.getElementById('discReqName').textContent = requester.name;
        document.getElementById('discReqId').textContent = requester.id;
        document.getElementById('discReqWarnings').textContent = requester.warnings;
        currentRequesterId = requester.id;
        currentRequesterRole = requester.role;
        currentRequesterWarnings = parseInt(requester.warnings || 0, 10);

        document.getElementById('discProvName').textContent = provider.name;
        document.getElementById('discProvId').textContent = provider.id;
        document.getElementById('discProvWarnings').textContent = provider.warnings;
        currentProviderId = provider.id;
        currentProviderRole = provider.role;
        currentProviderWarnings = parseInt(provider.warnings || 0, 10);

        const baseUrl = `${resolveBaseUrl}/${requestId}/resolve`;
        document.getElementById('disciplineForm').action = baseUrl;
        document.getElementById('dismissForm').action = baseUrl;
        document.getElementById('resumeForm').action = baseUrl;
        document.getElementById('completePaidForm').action = baseUrl;
        document.getElementById('actionPreview').classList.add('hidden');
        document.getElementById('actionPreview').textContent = '';

        document.getElementById('disciplineModal').classList.remove('hidden');
    };

    window.closeDisciplineModal = function () {
        document.getElementById('disciplineModal').classList.add('hidden');
        document.getElementById('actionPreview').classList.add('hidden');
    };

    window.submitDiscipline = function (action, target) {
        const noteInput = document.getElementById('adminNoteInput');
        const noteValue = noteInput.value.trim();
        if (!noteValue) {
            Swal.fire({
                icon: 'warning',
                title: 'Message Required',
                text: 'Please write a warning message or reason in the text box before proceeding.',
                confirmButtonColor: '#3085d6',
            }).then(() => setTimeout(() => noteInput.focus(), 300));
            return;
        }

        const targetId = target === 'requester' ? currentRequesterId : currentProviderId;
        const targetName = target === 'requester'
            ? document.getElementById('discReqName').textContent
            : document.getElementById('discProvName').textContent;
        const targetWarnings = target === 'requester' ? currentRequesterWarnings : currentProviderWarnings;

        if (action === 'warn' && targetWarnings >= warningLimit) {
            Swal.fire({
                icon: 'info',
                title: 'Warning Limit Reached',
                text: `This user is already at ${warningLimit}/${warningLimit}. Use Suspend/Blacklist for the next action.`,
                confirmButtonColor: '#3085d6',
            });
            return;
        }

        const targetRole = target === 'requester' ? currentRequesterRole : currentProviderRole;
        const previewEl = document.getElementById('actionPreview');
        let previewMsg;
        let confirmMsg;

        if (action === 'suspend_or_blacklist') {
            const penaltyLabel = targetRole === 'community' ? 'BLACKLIST' : 'SUSPEND';
            previewMsg = targetRole === 'community'
                ? 'Preview: Sends blacklist email, sets account to blacklisted, and cancels this request.'
                : 'Preview: Sends suspension email, sets account to suspended, and cancels this request.';
            confirmMsg = `Are you sure you want to ${penaltyLabel} ${targetName}?\n${previewMsg}`;
        } else {
            previewMsg = 'Preview: Sends warning email, increments warning count, and resumes this request to Waiting Payment.';
            confirmMsg = `Send this warning to ${targetName}?\n${previewMsg}`;
        }

        previewEl.textContent = previewMsg;
        previewEl.className = 'text-[11px] mt-2 rounded-md px-2 py-1 border bg-indigo-50 border-indigo-200 text-indigo-800';
        previewEl.classList.remove('hidden');

        if (confirm(confirmMsg)) {
            document.getElementById('inputActionType').value = action;
            document.getElementById('inputTargetUserId').value = targetId;
            document.getElementById('disciplineForm').submit();
        }
    };

    document.addEventListener('click', (event) => {
        const viewTrigger = event.target.closest('[data-request-open-view]');
        if (viewTrigger) {
            const req = JSON.parse(viewTrigger.dataset.request || '{}');
            const service = JSON.parse(viewTrigger.dataset.service || '{}');
            const requester = JSON.parse(viewTrigger.dataset.requester || '{}');
            const provider = JSON.parse(viewTrigger.dataset.provider || '{}');
            window.openViewModal(req, service, requester, provider);
            return;
        }

        const disciplineTrigger = event.target.closest('[data-request-open-discipline]');
        if (disciplineTrigger) {
            const requestId = disciplineTrigger.dataset.requestId;
            const reason = JSON.parse(disciplineTrigger.dataset.disputeReason || '""');
            const requesterPayload = JSON.parse(disciplineTrigger.dataset.requesterPayload || '{}');
            const providerPayload = JSON.parse(disciplineTrigger.dataset.providerPayload || '{}');
            const reporterPayload = JSON.parse(disciplineTrigger.dataset.reporterPayload || '{}');
            window.openDisciplineModal(requestId, reason, requesterPayload, providerPayload, reporterPayload);
            return;
        }

        if (event.target.closest('[data-request-close-view]')) {
            window.closeViewModal();
            return;
        }

        if (event.target.closest('[data-request-close-discipline]')) {
            window.closeDisciplineModal();
            return;
        }

        const disciplineSubmit = event.target.closest('[data-request-discipline-submit]');
        if (disciplineSubmit) {
            window.submitDiscipline(disciplineSubmit.dataset.action, disciplineSubmit.dataset.targetRole);
        }
    });
});

window.UServeAdmin.boot('requests');
