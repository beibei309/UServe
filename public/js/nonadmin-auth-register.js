(() => {
    const config = document.getElementById('registerConfig');
    if (!config || config.dataset.bound === 'true') return;
    config.dataset.bound = 'true';

    const form = document.getElementById('registerForm');
    if (!form) return;

    const roleInputs = Array.from(form.querySelectorAll('input[name="role"]'));
    const communityTypeInputs = Array.from(form.querySelectorAll('input[name="community_type"]'));
    const roleCards = Array.from(form.querySelectorAll('[data-role-card]'));
    const roleChecks = Array.from(form.querySelectorAll('[data-role-check]'));
    const roleSections = Array.from(form.querySelectorAll('[data-role-section]'));
    const communityMessages = Array.from(form.querySelectorAll('[data-community-message]'));

    const setRole = (role) => {
        roleCards.forEach((card) => {
            const isActive = card.dataset.roleCard === role;
            const activeClass = card.dataset.activeClass || '';
            const inactiveClass = card.dataset.inactiveClass || '';
            activeClass.split(' ').filter(Boolean).forEach((className) => card.classList.toggle(className, isActive));
            inactiveClass.split(' ').filter(Boolean).forEach((className) => card.classList.toggle(className, !isActive));
        });

        roleChecks.forEach((check) => {
            check.classList.toggle('hidden', check.dataset.roleCheck !== role);
        });

        roleSections.forEach((section) => {
            section.classList.toggle('hidden', section.dataset.roleSection !== role);
        });
    };

    const setCommunityType = (communityType) => {
        communityMessages.forEach((message) => {
            message.classList.toggle('hidden', message.dataset.communityMessage !== communityType);
        });
    };

    form.addEventListener('change', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLInputElement)) return;
        if (target.name === 'role') {
            setRole(target.value);
            return;
        }
        if (target.name === 'community_type') {
            setCommunityType(target.value);
        }
    });

    const initialRole = roleInputs.find((input) => input.checked)?.value || config.dataset.initialRole || 'student';
    const initialCommunityType = communityTypeInputs.find((input) => input.checked)?.value || config.dataset.initialCommunityType || 'public';
    setRole(initialRole);
    setCommunityType(initialCommunityType);
})();
