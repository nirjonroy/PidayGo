<script>
document.addEventListener('DOMContentLoaded', function () {
    var reserveLoader = document.getElementById('reserve-loader');
    var reserveLoaderTitle = document.getElementById('reserve-loader-title');
    var reserveLoaderCopy = document.getElementById('reserve-loader-copy');
    var levelSelect = document.getElementById('reserve-level-select');
    var planSelect = document.getElementById('reserve-plan-select');
    var hiddenPlanId = document.getElementById('reserve_plan_id');
    var hiddenRangeId = document.getElementById('reserve_plan_range_id');
    var submitButton = document.getElementById('reserve-plan-submit');
    var buyPiButton = document.getElementById('reserve-go-buy-pi');
    var selectedStatus = document.getElementById('reserve-selected-status');
    var selectedAmount = document.getElementById('reserve-selected-amount');
    var selectedProfit = document.getElementById('reserve-selected-profit');
    var selectedDailyLimit = document.getElementById('reserve-selected-daily-limit');
    var selectedRemaining = document.getElementById('reserve-selected-remaining');
    var selectedNote = document.getElementById('reserve-selected-note');
    var sellModal = document.getElementById('reserve-sell-modal');
    var openSellButtons = document.querySelectorAll('[data-open-reserve-modal]');
    var closeSellButtons = document.querySelectorAll('[data-close-reserve-modal]');
    var reserveSaleAmount = document.getElementById('reserve-sale-amount');
    var reserveSelectedImage = document.getElementById('reserve-selected-nft-image');
    var reserveSelectedTitle = document.getElementById('reserve-selected-nft-title');
    var reserveSelectedPrice = document.getElementById('reserve-selected-nft-price');
    var planDataElement = document.getElementById('reserve-plan-data');
    var plans = planDataElement ? JSON.parse(planDataElement.textContent || '[]') : [];
    var shouldAutoOpenSellModal = @json((bool) session('open_sell_modal'));

    function startCountdowns() {
        var countdowns = document.querySelectorAll('[data-countdown-target]');
        if (!countdowns.length) return;

        function formatCountdown(diffMs) {
            var totalSeconds = Math.max(0, Math.floor(diffMs / 1000));
            var days = Math.floor(totalSeconds / 86400);
            var hours = Math.floor((totalSeconds % 86400) / 3600);
            var minutes = Math.floor((totalSeconds % 3600) / 60);
            var seconds = totalSeconds % 60;
            var time = [hours, minutes, seconds].map(function (part) {
                return String(part).padStart(2, '0');
            }).join(':');

            return days > 0 ? days + 'd ' + time : time;
        }

        function updateCountdowns() {
            var now = Date.now();

            countdowns.forEach(function (node) {
                var targetValue = node.getAttribute('data-countdown-target');
                if (!targetValue) return;

                var targetTime = Date.parse(targetValue);
                if (Number.isNaN(targetTime)) return;

                var diff = targetTime - now;
                var prefix = node.getAttribute('data-countdown-prefix') || '';
                var expiredText = node.getAttribute('data-countdown-expired') || 'Unlocked now.';

                node.textContent = diff <= 0
                    ? expiredText
                    : prefix + formatCountdown(diff);
            });
        }

        updateCountdowns();
        window.setInterval(updateCountdowns, 1000);
    }

    function setLoaderCopy(title, copy) {
        if (reserveLoaderTitle && title) reserveLoaderTitle.textContent = title;
        if (reserveLoaderCopy && copy) reserveLoaderCopy.textContent = copy;
    }
    function showLoader(title, copy) {
        if (!reserveLoader) return;
        setLoaderCopy(title, copy);
        reserveLoader.classList.add('is-visible');
        reserveLoader.setAttribute('aria-hidden', 'false');
    }
    function openSellModal() {
        if (!sellModal) return;
        sellModal.classList.add('is-visible');
        sellModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('reserve-modal-open');
    }
    function closeSellModal() {
        if (!sellModal) return;
        sellModal.classList.remove('is-visible');
        sellModal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('reserve-modal-open');
    }
    function groupPlansByLevel() {
        return plans.reduce(function (carry, plan) {
            if (!carry[plan.levelId]) carry[plan.levelId] = [];
            carry[plan.levelId].push(plan);
            return carry;
        }, {});
    }
    function populatePlanOptions(levelId) {
        if (!planSelect) return;
        var groupedPlans = groupPlansByLevel();
        var levelPlans = groupedPlans[levelId] || [];
        planSelect.innerHTML = '';
        levelPlans.forEach(function (plan) {
            var option = document.createElement('option');
            option.value = String(plan.id);
            option.textContent = plan.rangeLabel;
            planSelect.appendChild(option);
        });
        if (levelPlans.length > 0) {
            var activePlan = levelPlans.find(function (plan) { return plan.isActiveOption; });
            var availablePlan = levelPlans.find(function (plan) { return plan.canReserve; });
            var unlockedPlan = levelPlans.find(function (plan) { return plan.isUnlocked; });
            planSelect.value = String((activePlan || availablePlan || unlockedPlan || levelPlans[0]).id);
        }
    }
    function updatePlanDetails() {
        if (!planSelect) return;
        var selectedPlan = plans.find(function (plan) { return String(plan.id) === String(planSelect.value); });
        if (!selectedPlan) return;

        if (hiddenPlanId) hiddenPlanId.value = selectedPlan.planId;
        if (hiddenRangeId) hiddenRangeId.value = selectedPlan.id;
        if (selectedStatus) {
            selectedStatus.textContent = selectedPlan.isActiveOption
                ? (selectedPlan.activeSellUnlocked ? 'Sell Ready' : 'Locked')
                : (selectedPlan.canReserve ? 'Available' : 'Blocked');
            selectedStatus.classList.remove('is-available', 'is-progress', 'is-blocked');
            selectedStatus.classList.add(
                selectedPlan.isActiveOption
                    ? (selectedPlan.activeSellUnlocked ? 'is-progress' : 'is-blocked')
                    : (selectedPlan.canReserve ? 'is-available' : 'is-blocked')
            );
        }
        if (selectedAmount) selectedAmount.textContent = selectedPlan.reserveAmountLabel;
        if (selectedProfit) selectedProfit.textContent = selectedPlan.profitRange;
        if (selectedDailyLimit) selectedDailyLimit.textContent = selectedPlan.dailyLimit;
        if (selectedRemaining) selectedRemaining.textContent = selectedPlan.remainingToday;
        if (selectedNote) selectedNote.textContent = 'Level amount range: ' + selectedPlan.rangeLabel + ' | Reserve amount: ' + selectedPlan.reserveAmountLabel + ' | Daily limit: ' + selectedPlan.dailyLimit + ' | ' + selectedPlan.note;

        if (selectedPlan.isActiveOption) {
            if (submitButton) submitButton.style.display = 'none';
            if (buyPiButton) {
                buyPiButton.style.display = 'inline-flex';
                buyPiButton.disabled = !selectedPlan.activeSellUnlocked;
                buyPiButton.textContent = selectedPlan.activeSellUnlocked
                    ? 'Sell PI Now'
                    : 'Locked Until 6 AM';
            }
            return;
        }

        if (buyPiButton) buyPiButton.style.display = 'none';
        if (submitButton) {
            submitButton.style.display = 'inline-flex';
            submitButton.disabled = !selectedPlan.canReserve;
            submitButton.textContent = selectedPlan.actionLabel;
        }
    }

    if (levelSelect && planSelect && plans.length > 0) {
        populatePlanOptions(levelSelect.value);
        updatePlanDetails();
        levelSelect.addEventListener('change', function () {
            populatePlanOptions(levelSelect.value);
            updatePlanDetails();
        });
        planSelect.addEventListener('change', updatePlanDetails);
    }

    openSellButtons.forEach(function (button) {
        button.addEventListener('click', openSellModal);
    });
    closeSellButtons.forEach(function (button) {
        button.addEventListener('click', closeSellModal);
    });
    if (sellModal) {
        sellModal.addEventListener('click', function (event) {
            if (event.target === sellModal) closeSellModal();
        });
    }
    document.querySelectorAll('.reserve-nft-select').forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (reserveSelectedImage) reserveSelectedImage.src = this.dataset.image || '';
            if (reserveSelectedTitle) reserveSelectedTitle.textContent = this.dataset.title || '';
            if (reserveSelectedPrice && reserveSaleAmount) {
                reserveSelectedPrice.textContent = 'Reserve Amount: ' + parseFloat(reserveSaleAmount.value || 0).toFixed(8) + ' USDT';
            }
        });
    });
    document.querySelectorAll('.reserve-start-form, .reserve-sell-form').forEach(function (form) {
        form.addEventListener('submit', function () {
            showLoader(form.dataset.loaderTitle || 'Processing', form.dataset.loaderCopy || 'Please wait while we complete your request.');
        });
    });
    startCountdowns();
    if (shouldAutoOpenSellModal && sellModal && !document.getElementById('notif-modal')) {
        openSellModal();
    }
});
</script>
