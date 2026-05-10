const API_KEY = "4KK8F1FTPMJLI2XE";
const USE_REAL_APIS = true;

const CURRENCIES = {
    PHP: { code: "PHP", locale: "en-PH", symbol: "₱" },
    USD: { code: "USD", locale: "en-US", symbol: "$" },
    EUR: { code: "EUR", locale: "de-DE", symbol: "€" },
    GBP: { code: "GBP", locale: "en-GB", symbol: "£" },
    JPY: { code: "JPY", locale: "ja-JP", symbol: "¥" },
    KRW: { code: "KRW", locale: "ko-KR", symbol: "₩" },
    SGD: { code: "SGD", locale: "en-SG", symbol: "S$" },
    AUD: { code: "AUD", locale: "en-AU", symbol: "A$" },
    CAD: { code: "CAD", locale: "en-CA", symbol: "C$" }
};

const ACCOUNT_TYPES = {
    bank: { label: "Bank", color: "#2f6df6", icon: "🏦" },
    wallet: { label: "E-Wallet", color: "#8459f3", icon: "📱" },
    cash: { label: "Cash", color: "#18c37e", icon: "💵" },
    investment: { label: "Investment", color: "#f59e0b", icon: "📈" }
};

const MARKET_ASSETS = [
    { type: "crypto", name: "Bitcoin", code: "BTC" },
    { type: "crypto", name: "Ethereum", code: "ETH" },
    { type: "equity", name: "Tesla", code: "TSLA" },
    { type: "equity", name: "Apple", code: "AAPL" },
    { type: "equity", name: "Microsoft", code: "MSFT" },
    { type: "equity", name: "NVIDIA", code: "NVDA" }
];

const DEFAULT_MARKET_DATA = [
    { name: "Bitcoin", code: "BTC", price: 65715.64, change: 1.43, points: [38, 55, 44, 45, 54, 56, 46, 36] },
    { name: "Ethereum", code: "ETH", price: 3347.99, change: 2.76, points: [41, 46, 42, 38, 44, 45, 48, 56] },
    { name: "Tesla", code: "TSLA", price: 177.58, change: -1.74, points: [44, 43, 44, 47, 56, 52, 38, 48] },
    { name: "Apple", code: "AAPL", price: 188.15, change: 1.18, points: [40, 42, 43, 45, 44, 46, 48, 50] },
    { name: "Microsoft", code: "MSFT", price: 421.30, change: 0.96, points: [36, 38, 39, 40, 43, 44, 46, 47] },
    { name: "NVIDIA", code: "NVDA", price: 903.22, change: 4.22, points: [32, 36, 42, 45, 49, 54, 57, 64] }
];

const DEFAULT_NEWS = [
    {
        source: "Demo Feed",
        time: "Now",
        title: "Markets rise as tech leads early momentum",
        summary: "Technology names continue to support risk appetite while traders watch inflation signals and interest-rate expectations."
    },
    {
        source: "Demo Feed",
        time: "Now",
        title: "Oil and gold remain in focus amid cautious trading",
        summary: "Commodity prices stay active as investors balance defensive positioning with appetite for growth assets."
    },
    {
        source: "Demo Feed",
        time: "Now",
        title: "Investors rotate into quality stocks after volatile week",
        summary: "Portfolio managers emphasize stronger balance sheets and stable earnings as short-term uncertainty remains."
    }
];

const MAX_ACCOUNT_NAME_LENGTH = 30;
const MAX_WHOLE_DIGITS = 12;
const MAX_BALANCE = 999999999999.99;

const defaultAccounts = [
    { id: cryptoId(), name: "Gcash", type: "wallet", balance: 800 },
    { id: cryptoId(), name: "GoTyme", type: "wallet", balance: 2200 },
    { id: cryptoId(), name: "BDO", type: "bank", balance: 900 }
];

const defaultHistory = [
    { id: cryptoId(), date: daysAgo(26), total: 1200, label: shortLabel(daysAgo(26)) },
    { id: cryptoId(), date: daysAgo(21), total: 1500, label: shortLabel(daysAgo(21)) },
    { id: cryptoId(), date: daysAgo(18), total: 1700, label: shortLabel(daysAgo(18)) },
    { id: cryptoId(), date: daysAgo(14), total: 2200, label: shortLabel(daysAgo(14)) },
    { id: cryptoId(), date: daysAgo(10), total: 2600, label: shortLabel(daysAgo(10)) },
    { id: cryptoId(), date: daysAgo(7), total: 3000, label: shortLabel(daysAgo(7)) },
    { id: cryptoId(), date: daysAgo(5), total: 3300, label: shortLabel(daysAgo(5)) },
    { id: cryptoId(), date: daysAgo(3), total: 3600, label: shortLabel(daysAgo(3)) },
    { id: cryptoId(), date: daysAgo(1), total: 3900, label: shortLabel(daysAgo(1)) }
];

let accounts = loadJSON("netWorthAccounts", defaultAccounts);
let history = loadJSON("netWorthHistory", defaultHistory);
let selectedCurrency = loadValue("selectedCurrency", "PHP");
let isDarkMode = loadValue("theme", "dark") === "dark";
let selectedHistoryRange = "7D";
let marketData = structuredClone(DEFAULT_MARKET_DATA);
let newsData = structuredClone(DEFAULT_NEWS);
let marketSourceMode = "demo";

const body = document.body;
const totalNetWorthElement = document.getElementById("totalNetWorth");
const currencySymbolElement = document.getElementById("currencySymbol");
const currencySelect = document.getElementById("currencySelect");
const themeToggleBtn = document.getElementById("themeToggle");

const marketCardsElement = document.getElementById("marketCards");
const moversCardsElement = document.getElementById("moversCards");
const newsListElement = document.getElementById("newsList");
const marketInsightBox = document.getElementById("marketInsightBox");
const marketPrevBtn = document.getElementById("marketPrevBtn");
const marketNextBtn = document.getElementById("marketNextBtn");
const marketRefreshBtn = document.getElementById("marketRefreshBtn");
const marketModeLabel = document.getElementById("marketModeLabel");

const accountsListElement = document.getElementById("accountsList");
const emptyStateElement = document.getElementById("emptyState");
const distributionBarElement = document.getElementById("distributionBar");
const distributionLegendElement = document.getElementById("distributionLegend");

const modal = document.getElementById("accountModal");
const addAccountBtn = document.getElementById("addAccountBtn");
const closeModalBtn = document.getElementById("closeModalBtn");
const cancelModalBtn = document.getElementById("cancelModalBtn");
const accountForm = document.getElementById("accountForm");
const modalTitle = document.getElementById("modalTitle");

const accountNameInput = document.getElementById("accountName");
const accountTypeInput = document.getElementById("accountType");
const accountBalanceInput = document.getElementById("accountBalance");
const editAccountIdInput = document.getElementById("editAccountId");
const nameError = document.getElementById("nameError");
const balanceError = document.getElementById("balanceError");

const chartGrid = document.getElementById("chartGrid");
const historyLinePath = document.getElementById("historyLinePath");
const historyAreaPath = document.getElementById("historyAreaPath");
const historyPoints = document.getElementById("historyPoints");
const historyLabels = document.getElementById("historyLabels");
const historyEmpty = document.getElementById("historyEmpty");
const historyDeltaValue = document.getElementById("historyDeltaValue");
const historyHighestValue = document.getElementById("historyHighestValue");
const historyPointsCount = document.getElementById("historyPointsCount");

const historyEntriesList = document.getElementById("historyEntriesList");
const historyModal = document.getElementById("historyModal");
const openHistoryModalBtn = document.getElementById("openHistoryModalBtn");
const closeHistoryModalBtn = document.getElementById("closeHistoryModalBtn");
const cancelHistoryModalBtn = document.getElementById("cancelHistoryModalBtn");
const historyForm = document.getElementById("historyForm");
const historyModalTitle = document.getElementById("historyModalTitle");
const editHistoryIdInput = document.getElementById("editHistoryId");
const historyDateInput = document.getElementById("historyDate");
const historyAmountInput = document.getElementById("historyAmount");
const historyDateError = document.getElementById("historyDateError");
const historyAmountError = document.getElementById("historyAmountError");

const newsModal = document.getElementById("newsModal");
const closeNewsModalBtn = document.getElementById("closeNewsModalBtn");
const closeNewsModalBtn2 = document.getElementById("closeNewsModalBtn2");
const newsModalTitle = document.getElementById("newsModalTitle");
const newsModalMeta = document.getElementById("newsModalMeta");
const newsModalSummary = document.getElementById("newsModalSummary");

init();

async function init() {
    normalizeHistory();
    sanitizeStoredAccounts();
    sanitizeStoredHistory();
    setupTheme();
    setupCurrency();
    setupEventListeners();
    renderNews();
    renderMarketSection();
    renderMovers();
    renderInsight();
    renderApp();
    await refreshExternalData();
    setInterval(refreshExternalData, 1000 * 60 * 5);
}

function setupEventListeners() {
    themeToggleBtn.addEventListener("click", toggleTheme);

    addAccountBtn.addEventListener("click", () => openModal());
    closeModalBtn.addEventListener("click", closeModal);
    cancelModalBtn.addEventListener("click", closeModal);
    accountForm.addEventListener("submit", handleAccountSubmit);

    modal.addEventListener("click", (event) => {
        if (event.target === modal) closeModal();
    });

    openHistoryModalBtn.addEventListener("click", () => openHistoryModal());
    closeHistoryModalBtn.addEventListener("click", closeHistoryModal);
    cancelHistoryModalBtn.addEventListener("click", closeHistoryModal);
    historyForm.addEventListener("submit", handleHistorySubmit);

    historyModal.addEventListener("click", (event) => {
        if (event.target === historyModal) closeHistoryModal();
    });

    historyAmountInput.addEventListener("input", handleHistoryAmountInput);
    historyAmountInput.addEventListener("blur", handleHistoryAmountBlur);

    newsModal.addEventListener("click", (event) => {
        if (event.target === newsModal) closeNewsModal();
    });

    closeNewsModalBtn.addEventListener("click", closeNewsModal);
    closeNewsModalBtn2.addEventListener("click", closeNewsModal);

    currencySelect.addEventListener("change", (event) => {
        selectedCurrency = event.target.value;
        saveState();
        updateCurrencyUI();
        updateBalancePlaceholder();
        updateHistoryAmountPlaceholder();
        renderMarketSection();
        renderMovers();
        renderInsight();
        renderApp();

        if (accountBalanceInput.value.trim()) {
            const numericValue = parseCurrencyInput(accountBalanceInput.value);
            accountBalanceInput.value = numericValue !== null ? formatCurrencyInputDisplay(numericValue) : "";
        }

        if (historyAmountInput.value.trim()) {
            const numericValue = parseCurrencyInput(historyAmountInput.value);
            historyAmountInput.value = numericValue !== null ? formatCurrencyInputDisplay(numericValue) : "";
        }
    });

    accountNameInput.addEventListener("input", handleNameInput);
    accountBalanceInput.addEventListener("input", handleBalanceInput);
    accountBalanceInput.addEventListener("blur", handleBalanceBlur);

    document.querySelectorAll(".range-btn").forEach((button) => {
        button.addEventListener("click", () => {
            document.querySelectorAll(".range-btn").forEach((btn) => btn.classList.remove("active"));
            button.classList.add("active");
            selectedHistoryRange = button.dataset.range;
            renderHistoryChart();
        });
    });

    marketPrevBtn.addEventListener("click", () => {
        marketCardsElement.scrollBy({ left: -240, behavior: "smooth" });
    });

    marketNextBtn.addEventListener("click", () => {
        marketCardsElement.scrollBy({ left: 240, behavior: "smooth" });
    });

    marketRefreshBtn.addEventListener("click", refreshExternalData);
    window.addEventListener("resize", renderHistoryChart);
}

function setupTheme() {
    if (isDarkMode) {
        body.removeAttribute("data-theme");
        themeToggleBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <circle cx="12" cy="12" r="4"></circle>
                <path d="M12 2v2"></path>
                <path d="M12 20v2"></path>
                <path d="m4.93 4.93 1.41 1.41"></path>
                <path d="m17.66 17.66 1.41 1.41"></path>
                <path d="M2 12h2"></path>
                <path d="M20 12h2"></path>
                <path d="m6.34 17.66-1.41 1.41"></path>
                <path d="m19.07 4.93-1.41 1.41"></path>
            </svg>
        `;
    } else {
        body.setAttribute("data-theme", "light");
        themeToggleBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
            </svg>
        `;
    }
}

function toggleTheme() {
    isDarkMode = !isDarkMode;
    saveState();
    setupTheme();
}

function setupCurrency() {
    if (!CURRENCIES[selectedCurrency]) selectedCurrency = "PHP";
    currencySelect.value = selectedCurrency;
    updateCurrencyUI();
    updateBalancePlaceholder();
    updateHistoryAmountPlaceholder();
}

function updateCurrencyUI() {
    currencySymbolElement.textContent = getCurrencySymbol();
}

function updateBalancePlaceholder() {
    accountBalanceInput.placeholder = `${getCurrencySymbol()}0.00`;
}

function updateHistoryAmountPlaceholder() {
    historyAmountInput.placeholder = `${getCurrencySymbol()}0.00`;
}

function getCurrencySymbol() {
    return CURRENCIES[selectedCurrency]?.symbol || "₱";
}

function renderApp() {
    const totalBalance = getTotalBalance();
    animateNumber(totalNetWorthElement, totalBalance);
    renderAccountsList();
    renderDistributionChart(totalBalance);
    renderHistoryChart();
    renderHistoryEntries();
}

function renderAccountsList() {
    if (accounts.length === 0) {
        accountsListElement.innerHTML = "";
        emptyStateElement.classList.remove("hidden");
        document.querySelector(".chart-section")?.classList.add("hidden");
        return;
    }

    emptyStateElement.classList.add("hidden");
    document.querySelector(".chart-section")?.classList.remove("hidden");
    accountsListElement.innerHTML = "";

    accounts.forEach((account) => {
        const typeInfo = ACCOUNT_TYPES[account.type] || ACCOUNT_TYPES.cash;
        const card = document.createElement("div");
        card.className = "account-card";
        card.innerHTML = `
            <div class="account-info">
                <div class="account-icon" style="background-color:${typeInfo.color}">
                    ${typeInfo.icon}
                </div>
                <div class="account-details">
                    <h3 title="${escapeHtml(account.name)}">${escapeHtml(account.name)}</h3>
                    <span class="account-type">${typeInfo.label}</span>
                </div>
            </div>

            <div class="account-actions">
                <div class="account-balance">${formatCurrency(Number(account.balance || 0))}</div>
                <div class="card-actions">
                    <button class="icon-btn edit-btn" title="Edit" data-id="${account.id}" type="button">✏️</button>
                    <button class="icon-btn delete-btn" title="Delete" data-id="${account.id}" type="button">🗑️</button>
                </div>
            </div>
        `;
        accountsListElement.appendChild(card);
    });

    document.querySelectorAll(".edit-btn").forEach((button) => {
        button.addEventListener("click", (event) => {
            openModal(event.currentTarget.getAttribute("data-id"));
        });
    });

    document.querySelectorAll(".delete-btn").forEach((button) => {
        button.addEventListener("click", (event) => {
            deleteAccount(event.currentTarget.getAttribute("data-id"));
        });
    });
}

function renderDistributionChart(totalBalance) {
    if (totalBalance <= 0) {
        distributionBarElement.innerHTML = '<div class="dist-segment" style="width:100%;background:var(--border-color);"></div>';
        distributionLegendElement.innerHTML = "";
        return;
    }

    const grouped = accounts.reduce((acc, current) => {
        const safeBalance = Number(current.balance || 0);
        acc[current.type] = (acc[current.type] || 0) + safeBalance;
        return acc;
    }, {});

    distributionBarElement.innerHTML = "";
    distributionLegendElement.innerHTML = "";

    Object.keys(grouped).forEach((type) => {
        const typeBalance = grouped[type];
        if (typeBalance <= 0) return;

        const percentage = (typeBalance / totalBalance) * 100;
        const typeInfo = ACCOUNT_TYPES[type] || ACCOUNT_TYPES.cash;

        const segment = document.createElement("div");
        segment.className = "dist-segment";
        segment.style.width = `${percentage}%`;
        segment.style.backgroundColor = typeInfo.color;
        segment.title = `${typeInfo.label}: ${formatCurrency(typeBalance)} (${percentage.toFixed(1)}%)`;
        distributionBarElement.appendChild(segment);

        const legendItem = document.createElement("div");
        legendItem.className = "legend-item";
        legendItem.innerHTML = `
            <div class="legend-color" style="background-color:${typeInfo.color}"></div>
            <span>${typeInfo.label} (${percentage.toFixed(0)}%)</span>
        `;
        distributionLegendElement.appendChild(legendItem);
    });
}

function renderHistoryEntries() {
    historyEntriesList.innerHTML = "";

    const sorted = [...history].sort((a, b) => new Date(b.date) - new Date(a.date));

    if (!sorted.length) {
        historyEntriesList.innerHTML = `
            <div class="history-entry-card">
                <div class="history-entry-left">
                    <div class="history-entry-date">No entries yet</div>
                    <div class="history-entry-amount">Add one to control the graph</div>
                </div>
            </div>
        `;
        return;
    }

    sorted.forEach((entry) => {
        const card = document.createElement("div");
        card.className = "history-entry-card";
        card.innerHTML = `
            <div class="history-entry-left">
                <div class="history-entry-date">${formatTooltipDate(entry.date)}</div>
                <div class="history-entry-amount">${formatCurrency(Number(entry.total || 0))}</div>
            </div>
            <div class="history-entry-actions">
                <button class="icon-btn edit-history-btn" type="button" data-id="${entry.id}" title="Edit history">✏️</button>
                <button class="icon-btn delete-history-btn" type="button" data-id="${entry.id}" title="Delete history">🗑️</button>
            </div>
        `;
        historyEntriesList.appendChild(card);
    });

    document.querySelectorAll(".edit-history-btn").forEach((button) => {
        button.addEventListener("click", () => openHistoryModal(button.dataset.id));
    });

    document.querySelectorAll(".delete-history-btn").forEach((button) => {
        button.addEventListener("click", () => deleteHistoryEntry(button.dataset.id));
    });
}

function renderHistoryChart() {
    chartGrid.innerHTML = "";
    historyPoints.innerHTML = "";
    historyLabels.innerHTML = "";

    const chartWrap = document.getElementById("historyChartWrap");
    const historyChart = document.getElementById("historyChart");
    const tooltip = document.getElementById("historyTooltip");
    const tooltipDate = document.getElementById("tooltipDate");
    const tooltipValue = document.getElementById("tooltipValue");
    const tooltipChange = document.getElementById("tooltipChange");
    const crosshair = document.getElementById("historyCrosshair");
    const activePoint = document.getElementById("historyActivePoint");
    const hitbox = document.getElementById("historyHitbox");

    const filteredHistory = getFilteredHistory();
    updateHistorySummary(filteredHistory);

    if (!filteredHistory || filteredHistory.length < 2) {
        historyLinePath.setAttribute("d", "");
        historyAreaPath.setAttribute("d", "");
        historyEmpty.classList.remove("hidden");
        tooltip.classList.add("hidden");
        crosshair.classList.add("hidden");
        activePoint.classList.add("hidden");

        if (filteredHistory.length === 1) {
            historyLabels.innerHTML = `<span>${filteredHistory[0].label}</span>`;
        }
        return;
    }

    historyEmpty.classList.add("hidden");

    const width = 600;
    const height = 260;
    const paddingX = 34;
    const paddingTop = 20;
    const paddingBottom = 38;
    const chartHeight = height - paddingTop - paddingBottom;
    const chartWidth = width - paddingX * 2;

    const values = filteredHistory.map((item) => Number(item.total || 0));
    let minValue = Math.min(...values);
    let maxValue = Math.max(...values);

    if (minValue === maxValue) {
        minValue -= 100;
        maxValue += 100;
    }

    const valueRange = maxValue - minValue;

    for (let i = 0; i < 4; i++) {
        const y = paddingTop + (chartHeight / 3) * i;
        const line = document.createElementNS("http://www.w3.org/2000/svg", "line");
        line.setAttribute("x1", paddingX);
        line.setAttribute("x2", width - paddingX);
        line.setAttribute("y1", y);
        line.setAttribute("y2", y);
        line.setAttribute("class", "chart-grid-line");
        chartGrid.appendChild(line);
    }

    const points = filteredHistory.map((item, index) => {
        const x = paddingX + index * (chartWidth / (filteredHistory.length - 1));
        const normalized = (Number(item.total || 0) - minValue) / valueRange;
        const y = paddingTop + chartHeight - normalized * chartHeight;

        return {
            x,
            y,
            value: Number(item.total || 0),
            label: item.label,
            rawDate: item.date
        };
    });

    const linePath = buildSmoothPath(points);
    historyLinePath.setAttribute("d", linePath);

    const areaPath = `
        ${linePath}
        L ${points[points.length - 1].x} ${height - paddingBottom}
        L ${points[0].x} ${height - paddingBottom}
        Z
    `;
    historyAreaPath.setAttribute("d", areaPath);

    points.forEach((point, index) => {
        const circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
        circle.setAttribute("cx", point.x);
        circle.setAttribute("cy", point.y);
        circle.setAttribute("r", 5);
        circle.setAttribute("class", "chart-point");
        circle.setAttribute("data-index", String(index));
        historyPoints.appendChild(circle);
    });

    filteredHistory.forEach((item) => {
        const label = document.createElement("span");
        label.textContent = item.label;
        historyLabels.appendChild(label);
    });

    const pointNodes = Array.from(historyPoints.querySelectorAll(".chart-point"));

    function setActivePoint(index) {
        const point = points[index];
        if (!point) return;

        pointNodes.forEach((node, nodeIndex) => {
            node.classList.toggle("is-active", nodeIndex === index);
            node.classList.toggle("is-dim", nodeIndex !== index);
        });

        tooltip.classList.remove("hidden");
        crosshair.classList.remove("hidden");
        activePoint.classList.remove("hidden");

        crosshair.setAttribute("x1", point.x);
        crosshair.setAttribute("x2", point.x);
        crosshair.setAttribute("y1", paddingTop);
        crosshair.setAttribute("y2", height - paddingBottom);

        activePoint.setAttribute("cx", point.x);
        activePoint.setAttribute("cy", point.y);

        tooltipDate.textContent = formatTooltipDate(point.rawDate);
        tooltipValue.textContent = formatCurrency(point.value);

        const delta = getHistoryDelta(filteredHistory, index);
        tooltipChange.className = "chart-tooltip-change";

        if (delta === null) {
            tooltipChange.textContent = "Starting point";
            tooltipChange.classList.add("neutral");
        } else if (delta > 0) {
            tooltipChange.textContent = `+${formatCurrency(Math.abs(delta))} vs previous`;
            tooltipChange.classList.add("positive");
        } else if (delta < 0) {
            tooltipChange.textContent = `-${formatCurrency(Math.abs(delta))} vs previous`;
            tooltipChange.classList.add("negative");
        } else {
            tooltipChange.textContent = "No change vs previous";
            tooltipChange.classList.add("neutral");
        }

        positionTooltip({
            tooltip,
            chartWrap,
            pointX: point.x,
            pointY: point.y,
            svgWidth: width,
            svgHeight: height
        });
    }

    function clearActivePoint() {
        pointNodes.forEach((node) => node.classList.remove("is-active", "is-dim"));
        tooltip.classList.add("hidden");
        crosshair.classList.add("hidden");
        activePoint.classList.add("hidden");
    }

    function handlePointerMove(event) {
        const svgPoint = getSvgPointerPosition(event, historyChart);
        const nearestIndex = getNearestPointIndex(svgPoint.x, points);
        setActivePoint(nearestIndex);
    }

    hitbox.onpointerdown = (event) => {
        hitbox.setPointerCapture(event.pointerId);
        handlePointerMove(event);
    };
    hitbox.onpointermove = handlePointerMove;
    hitbox.onpointerenter = handlePointerMove;
    hitbox.onpointerup = clearActivePoint;
    hitbox.onpointercancel = clearActivePoint;
    hitbox.onpointerleave = clearActivePoint;

    pointNodes.forEach((node) => {
        node.addEventListener("mouseenter", () => setActivePoint(Number(node.dataset.index)));
        node.addEventListener("click", () => setActivePoint(Number(node.dataset.index)));
    });

    setActivePoint(points.length - 1);
}

function updateHistorySummary(filteredHistory) {
    if (!filteredHistory.length) {
        historyDeltaValue.textContent = formatCurrency(0);
        historyHighestValue.textContent = formatCurrency(0);
        historyPointsCount.textContent = "0";
        return;
    }

    const first = Number(filteredHistory[0].total || 0);
    const last = Number(filteredHistory[filteredHistory.length - 1].total || 0);
    const highest = Math.max(...filteredHistory.map((item) => Number(item.total || 0)));

    historyDeltaValue.textContent = `${last >= first ? "+" : "-"}${formatCurrency(Math.abs(last - first))}`;
    historyHighestValue.textContent = formatCurrency(highest);
    historyPointsCount.textContent = String(filteredHistory.length);
}

function getFilteredHistory() {
    if (!history.length) return [];

    const sorted = [...history].sort((a, b) => new Date(a.date) - new Date(b.date));

    if (selectedHistoryRange === "ALL") {
        return sorted.slice(-12);
    }

    const now = new Date();
    const dayMap = { "7D": 7, "1M": 30, "3M": 90 };
    const days = dayMap[selectedHistoryRange] || 7;
    const cutoff = new Date(now);
    cutoff.setDate(cutoff.getDate() - days);

    const filtered = sorted.filter((item) => new Date(item.date) >= cutoff);
    return filtered.length >= 2 ? filtered : sorted.slice(-Math.min(sorted.length, 6));
}

function openHistoryModal(historyId = null) {
    historyModal.classList.remove("hidden");
    clearHistoryErrors();

    if (historyId) {
        const entry = history.find((item) => item.id === historyId);
        if (entry) {
            historyModalTitle.textContent = "Edit History Entry";
            editHistoryIdInput.value = entry.id;
            historyDateInput.value = toInputDate(entry.date);
            historyAmountInput.value = formatCurrencyInputDisplay(entry.total);
        }
    } else {
        historyModalTitle.textContent = "Add History Entry";
        historyForm.reset();
        editHistoryIdInput.value = "";
        historyDateInput.value = toInputDate(new Date().toISOString());
        updateHistoryAmountPlaceholder();
    }
}

function closeHistoryModal() {
    historyModal.classList.add("hidden");
    historyForm.reset();
    editHistoryIdInput.value = "";
    clearHistoryErrors();
    updateHistoryAmountPlaceholder();
}

function handleHistoryAmountInput(event) {
    event.target.value = sanitizeAndFormatBalanceValue(event.target.value);
    clearInputError(historyAmountInput, historyAmountError);
}

function handleHistoryAmountBlur() {
    const numericValue = parseCurrencyInput(historyAmountInput.value);
    if (numericValue !== null) {
        historyAmountInput.value = formatCurrencyInputDisplay(numericValue);
    }
}

function handleHistorySubmit(event) {
    event.preventDefault();

    const dateValue = historyDateInput.value;
    const amountValue = parseCurrencyInput(historyAmountInput.value);
    let isValid = true;

    if (!dateValue) {
        showInputError(historyDateInput, historyDateError, "Please select a date.");
        isValid = false;
    }

    if (amountValue === null) {
        showInputError(historyAmountInput, historyAmountError, "Please enter a valid amount.");
        isValid = false;
    } else {
        const safeString = Math.floor(Math.abs(amountValue)).toString();
        const wholeDigits = safeString.replace(/^0+/, "").length || 1;

        if (wholeDigits > MAX_WHOLE_DIGITS) {
            showInputError(historyAmountInput, historyAmountError, `Only up to ${MAX_WHOLE_DIGITS} whole digits allowed.`);
            isValid = false;
        }

        if (amountValue > MAX_BALANCE) {
            showInputError(historyAmountInput, historyAmountError, "Amount is too large.");
            isValid = false;
        }

        if (amountValue < 0) {
            showInputError(historyAmountInput, historyAmountError, "Amount cannot be negative.");
            isValid = false;
        }
    }

    if (!isValid) return;

    const isoDate = new Date(`${dateValue}T12:00:00`).toISOString();

    const payload = {
        id: editHistoryIdInput.value || cryptoId(),
        date: isoDate,
        total: Number(amountValue.toFixed(2)),
        label: shortLabel(isoDate)
    };

    const existingIndex = history.findIndex((item) => item.id === payload.id);

    if (existingIndex >= 0) {
        history[existingIndex] = payload;
    } else {
        history.push(payload);
    }

    sanitizeStoredHistory();
    saveState();
    renderApp();
    closeHistoryModal();
}

function deleteHistoryEntry(historyId) {
    history = history.filter((item) => item.id !== historyId);
    sanitizeStoredHistory();
    saveState();
    renderApp();
}

function openModal(accountId = null) {
    modal.classList.remove("hidden");
    clearFormErrors();

    if (accountId) {
        const account = accounts.find((item) => item.id === accountId);
        if (account) {
            modalTitle.textContent = "Edit Account";
            editAccountIdInput.value = account.id;
            accountNameInput.value = account.name;
            accountTypeInput.value = account.type;
            accountBalanceInput.value = formatCurrencyInputDisplay(account.balance);
        }
    } else {
        modalTitle.textContent = "Add New Account";
        accountForm.reset();
        editAccountIdInput.value = "";
        accountTypeInput.value = "bank";
        updateBalancePlaceholder();
    }
}

function closeModal() {
    modal.classList.add("hidden");
    accountForm.reset();
    clearFormErrors();
    editAccountIdInput.value = "";
    updateBalancePlaceholder();
}

function openNewsModal(newsItem) {
    newsModalTitle.textContent = newsItem.title || "News";
    newsModalMeta.textContent = `${newsItem.source || "Market News"} • ${newsItem.time || "Latest"}`;
    newsModalSummary.textContent = newsItem.summary || "No summary available.";
    newsModal.classList.remove("hidden");
}

function closeNewsModal() {
    newsModal.classList.add("hidden");
}

function handleNameInput() {
    if (accountNameInput.value.length > MAX_ACCOUNT_NAME_LENGTH) {
        accountNameInput.value = accountNameInput.value.slice(0, MAX_ACCOUNT_NAME_LENGTH);
    }
    clearInputError(accountNameInput, nameError);
}

function handleBalanceInput(event) {
    const formattedValue = sanitizeAndFormatBalanceValue(event.target.value);
    event.target.value = formattedValue;
    clearInputError(accountBalanceInput, balanceError);
}

function handleBalanceBlur() {
    const numericValue = parseCurrencyInput(accountBalanceInput.value);
    if (numericValue !== null) {
        accountBalanceInput.value = formatCurrencyInputDisplay(numericValue);
    }
}

function handleAccountSubmit(event) {
    event.preventDefault();

    const rawName = accountNameInput.value.trim();
    const balanceNumber = parseCurrencyInput(accountBalanceInput.value);
    let isValid = true;

    if (!rawName) {
        showInputError(accountNameInput, nameError, "Please enter an account name.");
        isValid = false;
    } else if (rawName.length > MAX_ACCOUNT_NAME_LENGTH) {
        showInputError(accountNameInput, nameError, `Maximum of ${MAX_ACCOUNT_NAME_LENGTH} characters only.`);
        isValid = false;
    }

    if (balanceNumber === null) {
        showInputError(accountBalanceInput, balanceError, "Please enter a valid amount.");
        isValid = false;
    } else {
        const safeString = Math.floor(Math.abs(balanceNumber)).toString();
        const wholeDigits = safeString.replace(/^0+/, "").length || 1;

        if (wholeDigits > MAX_WHOLE_DIGITS) {
            showInputError(accountBalanceInput, balanceError, `Only up to ${MAX_WHOLE_DIGITS} whole digits allowed.`);
            isValid = false;
        }

        if (balanceNumber > MAX_BALANCE) {
            showInputError(accountBalanceInput, balanceError, "Amount is too large.");
            isValid = false;
        }

        if (balanceNumber < 0) {
            showInputError(accountBalanceInput, balanceError, "Amount cannot be negative.");
            isValid = false;
        }
    }

    if (!isValid) return;

    const safeName = rawName.slice(0, MAX_ACCOUNT_NAME_LENGTH);

    const accountPayload = {
        id: editAccountIdInput.value || cryptoId(),
        name: safeName,
        type: accountTypeInput.value,
        balance: Number(balanceNumber.toFixed(2))
    };

    const existingIndex = accounts.findIndex((item) => item.id === accountPayload.id);

    if (existingIndex >= 0) {
        accounts[existingIndex] = accountPayload;
    } else {
        accounts.push(accountPayload);
    }

    sanitizeStoredAccounts();
    pushAutoHistorySnapshot();
    saveState();
    renderApp();
    closeModal();
}

function deleteAccount(accountId) {
    accounts = accounts.filter((item) => item.id !== accountId);
    sanitizeStoredAccounts();
    pushAutoHistorySnapshot();
    saveState();
    renderApp();
}

function pushAutoHistorySnapshot() {
    const total = getTotalBalance();
    const todayIso = new Date().toISOString();
    const todayInput = toInputDate(todayIso);

    const existingIndex = history.findIndex((item) => toInputDate(item.date) === todayInput);

    if (existingIndex >= 0) {
        history[existingIndex].total = Number(total.toFixed(2));
        history[existingIndex].date = todayIso;
        history[existingIndex].label = shortLabel(todayIso);
    } else {
        history.push({
            id: cryptoId(),
            date: todayIso,
            total: Number(total.toFixed(2)),
            label: shortLabel(todayIso)
        });
    }

    sanitizeStoredHistory();
}

function normalizeHistory() {
    history = history.map((item) => ({
        id: item.id || cryptoId(),
        date: item.date,
        total: Number(item.total || 0),
        label: item.label || shortLabel(item.date)
    }));
}

function sanitizeStoredAccounts() {
    accounts = accounts.map((account) => {
        const safeName = String(account.name || "").trim().slice(0, MAX_ACCOUNT_NAME_LENGTH);
        let safeBalance = Number(account.balance || 0);

        if (!Number.isFinite(safeBalance) || safeBalance < 0) safeBalance = 0;
        if (safeBalance > MAX_BALANCE) safeBalance = MAX_BALANCE;

        return {
            ...account,
            id: account.id || cryptoId(),
            name: safeName,
            balance: Number(safeBalance.toFixed(2))
        };
    });
}

function sanitizeStoredHistory() {
    history = history
        .map((entry) => {
            let safeTotal = Number(entry.total || 0);
            if (!Number.isFinite(safeTotal) || safeTotal < 0) safeTotal = 0;
            if (safeTotal > MAX_BALANCE) safeTotal = MAX_BALANCE;

            const safeDate = entry.date ? new Date(entry.date).toISOString() : new Date().toISOString();

            return {
                id: entry.id || cryptoId(),
                date: safeDate,
                total: Number(safeTotal.toFixed(2)),
                label: shortLabel(safeDate)
            };
        })
        .sort((a, b) => new Date(a.date) - new Date(b.date))
        .slice(-60);
}

function getTotalBalance() {
    return accounts.reduce((sum, account) => sum + Number(account.balance || 0), 0);
}

function formatCurrency(amount) {
    const config = CURRENCIES[selectedCurrency] || CURRENCIES.PHP;
    return new Intl.NumberFormat(config.locale, {
        style: "currency",
        currency: config.code,
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
}

function formatCurrencyNumberOnly(amount) {
    const config = CURRENCIES[selectedCurrency] || CURRENCIES.PHP;
    return new Intl.NumberFormat(config.locale, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
}

function formatMarketNumber(amount) {
    return formatCurrency(amount);
}

function formatTooltipDate(dateInput) {
    const date = new Date(dateInput);
    if (Number.isNaN(date.getTime())) return String(dateInput || "");
    return date.toLocaleDateString("en-PH", {
        month: "short",
        day: "numeric",
        year: "numeric"
    });
}

function shortLabel(dateInput) {
    const date = new Date(dateInput);
    return date.toLocaleDateString("en-PH", {
        month: "short",
        day: "numeric"
    });
}

function toInputDate(dateInput) {
    const date = new Date(dateInput);
    if (Number.isNaN(date.getTime())) return "";
    const year = date.getFullYear();
    const month = `${date.getMonth() + 1}`.padStart(2, "0");
    const day = `${date.getDate()}`.padStart(2, "0");
    return `${year}-${month}-${day}`;
}

function animateNumber(element, finalValue) {
    const duration = 800;
    const currentText = element.innerText.replace(/,/g, "");
    const startValue = parseFloat(currentText) || 0;
    const startTime = performance.now();

    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const easeOut = 1 - Math.pow(1 - progress, 4);
        const currentValue = startValue + (finalValue - startValue) * easeOut;

        element.innerText = formatCurrencyNumberOnly(currentValue);

        if (progress < 1) {
            requestAnimationFrame(update);
        } else {
            element.innerText = formatCurrencyNumberOnly(finalValue);
        }
    }

    requestAnimationFrame(update);
}

function parseCurrencyInput(value) {
    if (typeof value !== "string") return null;
    const cleaned = value.replace(/[^\d.]/g, "");
    if (!cleaned || cleaned === ".") return null;

    const parts = cleaned.split(".");
    if (parts.length > 2) return null;

    const normalized = parts[0] + (parts[1] !== undefined ? `.${parts[1]}` : "");
    const numeric = Number(normalized);
    if (!Number.isFinite(numeric)) return null;
    return numeric;
}

function sanitizeAndFormatBalanceValue(value) {
    let cleaned = String(value).replace(/[^\d.]/g, "");
    const firstDotIndex = cleaned.indexOf(".");

    if (firstDotIndex !== -1) {
        cleaned = cleaned.slice(0, firstDotIndex + 1) + cleaned.slice(firstDotIndex + 1).replace(/\./g, "");
    }

    let [whole = "", decimal = ""] = cleaned.split(".");
    whole = whole.slice(0, MAX_WHOLE_DIGITS);
    decimal = decimal.slice(0, 2);

    const wholeWithCommas = whole ? Number(whole).toLocaleString("en-US") : "";
    return decimal.length > 0
        ? `${getCurrencySymbol()}${wholeWithCommas}.${decimal}`
        : `${getCurrencySymbol()}${wholeWithCommas}`;
}

function formatCurrencyInputDisplay(value) {
    const numeric = Number(value || 0);
    return `${getCurrencySymbol()}${numeric.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })}`;
}

function showInputError(input, errorElement, message) {
    input.classList.add("input-error", "shake");
    if (errorElement) errorElement.textContent = message;
    setTimeout(() => input.classList.remove("shake"), 350);
}

function clearInputError(input, errorElement) {
    input.classList.remove("input-error");
    if (errorElement) errorElement.textContent = "";
}

function clearFormErrors() {
    clearInputError(accountNameInput, nameError);
    clearInputError(accountBalanceInput, balanceError);
}

function clearHistoryErrors() {
    clearInputError(historyDateInput, historyDateError);
    clearInputError(historyAmountInput, historyAmountError);
}

function buildSmoothPath(points) {
    if (points.length < 2) return "";
    let path = `M ${points[0].x} ${points[0].y}`;

    for (let i = 0; i < points.length - 1; i++) {
        const current = points[i];
        const next = points[i + 1];
        const controlX1 = current.x + (next.x - current.x) / 2;
        const controlY1 = current.y;
        const controlX2 = current.x + (next.x - current.x) / 2;
        const controlY2 = next.y;
        path += ` C ${controlX1} ${controlY1}, ${controlX2} ${controlY2}, ${next.x} ${next.y}`;
    }

    return path;
}

function buildSmoothSparklinePath(values) {
    if (!values.length) return "";
    const width = 100;
    const height = 54;
    const min = Math.min(...values);
    const max = Math.max(...values);
    const range = max - min || 1;

    const points = values.map((value, index) => {
        const x = (index / (values.length - 1)) * width;
        const y = height - ((value - min) / range) * (height - 8) - 4;
        return { x, y };
    });

    let path = `M ${points[0].x} ${points[0].y}`;

    for (let i = 0; i < points.length - 1; i++) {
        const current = points[i];
        const next = points[i + 1];
        const controlX1 = current.x + (next.x - current.x) / 2;
        const controlY1 = current.y;
        const controlX2 = current.x + (next.x - current.x) / 2;
        const controlY2 = next.y;
        path += ` C ${controlX1} ${controlY1}, ${controlX2} ${controlY2}, ${next.x} ${next.y}`;
    }

    return path;
}

function buildSparklineAreaPath(values) {
    if (!values.length) return "";
    const width = 100;
    const height = 54;
    const min = Math.min(...values);
    const max = Math.max(...values);
    const range = max - min || 1;

    const points = values.map((value, index) => {
        const x = (index / (values.length - 1)) * width;
        const y = height - ((value - min) / range) * (height - 8) - 4;
        return { x, y };
    });

    let path = `M ${points[0].x} ${height}`;
    path += ` L ${points[0].x} ${points[0].y}`;

    for (let i = 0; i < points.length - 1; i++) {
        const current = points[i];
        const next = points[i + 1];
        const controlX1 = current.x + (next.x - current.x) / 2;
        const controlY1 = current.y;
        const controlX2 = current.x + (next.x - current.x) / 2;
        const controlY2 = next.y;
        path += ` C ${controlX1} ${controlY1}, ${controlX2} ${controlY2}, ${next.x} ${next.y}`;
    }

    path += ` L ${points[points.length - 1].x} ${height} Z`;
    return path;
}

function getSparklineLastPoint(values) {
    const width = 100;
    const height = 54;
    const min = Math.min(...values);
    const max = Math.max(...values);
    const range = max - min || 1;
    const lastIndex = values.length - 1;
    const x = (lastIndex / (values.length - 1)) * width;
    const y = height - ((values[lastIndex] - min) / range) * (height - 8) - 4;
    return { x, y };
}

function getNearestPointIndex(x, points) {
    let nearestIndex = 0;
    let smallestDistance = Infinity;

    points.forEach((point, index) => {
        const distance = Math.abs(point.x - x);
        if (distance < smallestDistance) {
            smallestDistance = distance;
            nearestIndex = index;
        }
    });

    return nearestIndex;
}

function getSvgPointerPosition(event, svg) {
    const point = svg.createSVGPoint();
    point.x = event.clientX;
    point.y = event.clientY;
    const transformed = point.matrixTransform(svg.getScreenCTM().inverse());
    return { x: transformed.x, y: transformed.y };
}

function positionTooltip({ tooltip, chartWrap, pointX, pointY, svgWidth, svgHeight }) {
    const wrapRect = chartWrap.getBoundingClientRect();
    const relativeX = (pointX / svgWidth) * wrapRect.width;
    const relativeY = (pointY / svgHeight) * wrapRect.height;
    const tooltipWidth = tooltip.offsetWidth || 150;
    const tooltipHeight = tooltip.offsetHeight || 68;
    const gap = 14;

    let left = relativeX - tooltipWidth / 2;
    let top = relativeY - tooltipHeight - gap;

    const minLeft = 8;
    const maxLeft = wrapRect.width - tooltipWidth - 8;

    if (left < minLeft) left = minLeft;
    if (left > maxLeft) left = maxLeft;
    if (top < 8) top = relativeY + gap;

    tooltip.style.left = `${left}px`;
    tooltip.style.top = `${top}px`;
}

function getHistoryDelta(historyList, index) {
    if (index <= 0 || !historyList[index] || !historyList[index - 1]) return null;
    return Number(historyList[index].total || 0) - Number(historyList[index - 1].total || 0);
}

function timeAgoFromPublished(input) {
    if (!input) return "Latest";

    const year = input.slice(0, 4);
    const month = input.slice(4, 6);
    const day = input.slice(6, 8);
    const hour = input.slice(9, 11) || "00";
    const minute = input.slice(11, 13) || "00";

    const date = new Date(`${year}-${month}-${day}T${hour}:${minute}:00Z`);
    if (Number.isNaN(date.getTime())) return "Latest";

    const diffMs = Date.now() - date.getTime();
    const hours = Math.floor(diffMs / (1000 * 60 * 60));
    if (hours < 1) return "Just now";
    if (hours < 24) return `${hours}h ago`;
    const days = Math.floor(hours / 24);
    return `${days}d ago`;
}

function limitText(text, maxLength) {
    if (!text || text.length <= maxLength) return text;
    return `${text.slice(0, maxLength).trim()}...`;
}

function escapeHtml(value) {
    return String(value)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

async function refreshExternalData() {
    if (!USE_REAL_APIS || !API_KEY) {
        marketSourceMode = "demo";
        marketData = structuredClone(DEFAULT_MARKET_DATA);
        newsData = structuredClone(DEFAULT_NEWS);
        renderMarketSection();
        renderMovers();
        renderNews();
        renderInsight();
        return;
    }

    try {
        marketCardsElement.innerHTML = '<div class="loading-card">Fetching live market data…</div>';
        const fetchedMarket = await fetchRealMarketData();
        const fetchedNews = await fetchRealNews();

        if (fetchedMarket.length) {
            marketData = fetchedMarket;
            marketSourceMode = "api";
        } else {
            marketData = structuredClone(DEFAULT_MARKET_DATA);
            marketSourceMode = "demo";
        }

        if (fetchedNews.length) {
            newsData = fetchedNews;
        } else {
            newsData = structuredClone(DEFAULT_NEWS);
        }
    } catch (error) {
        console.error("API refresh failed:", error);
        marketSourceMode = "demo";
        marketData = structuredClone(DEFAULT_MARKET_DATA);
        newsData = structuredClone(DEFAULT_NEWS);
    }

    renderMarketSection();
    renderMovers();
    renderNews();
    renderInsight();
}

async function fetchRealMarketData() {
    const results = [];

    for (const asset of MARKET_ASSETS) {
        try {
            if (asset.type === "equity") {
                const item = await fetchEquityAsset(asset);
                if (item) results.push(item);
            } else {
                const item = await fetchCryptoAsset(asset);
                if (item) results.push(item);
            }
            await sleep(900);
        } catch (error) {
            console.warn(`Failed for ${asset.code}:`, error);
        }
    }

    return results;
}

async function fetchEquityAsset(asset) {
    const quoteUrl = `https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=${encodeURIComponent(asset.code)}&apikey=${encodeURIComponent(API_KEY)}`;
    const seriesUrl = `https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=${encodeURIComponent(asset.code)}&outputsize=compact&apikey=${encodeURIComponent(API_KEY)}`;

    const [quoteRes, seriesRes] = await Promise.all([fetchJSON(quoteUrl), fetchJSON(seriesUrl)]);
    const quote = quoteRes["Global Quote"] || {};
    const series = seriesRes["Time Series (Daily)"] || {};

    const price = Number(quote["05. price"]);
    const changePercent = Number(String(quote["10. change percent"] || "0").replace("%", ""));
    const points = extractDailySeriesPoints(series);

    if (!Number.isFinite(price) || !points.length) return null;

    return {
        name: asset.name,
        code: asset.code,
        price,
        change: Number.isFinite(changePercent) ? changePercent : 0,
        points
    };
}

async function fetchCryptoAsset(asset) {
    const rateUrl = `https://www.alphavantage.co/query?function=CURRENCY_EXCHANGE_RATE&from_currency=${encodeURIComponent(asset.code)}&to_currency=${encodeURIComponent(selectedCurrency)}&apikey=${encodeURIComponent(API_KEY)}`;
    const dailyUrl = `https://www.alphavantage.co/query?function=DIGITAL_CURRENCY_DAILY&symbol=${encodeURIComponent(asset.code)}&market=${encodeURIComponent(selectedCurrency)}&apikey=${encodeURIComponent(API_KEY)}`;

    const [rateRes, dailyRes] = await Promise.all([fetchJSON(rateUrl), fetchJSON(dailyUrl)]);
    const rateBlock = rateRes["Realtime Currency Exchange Rate"] || {};
    const dailyBlock = dailyRes["Time Series (Digital Currency Daily)"] || {};

    const price = Number(rateBlock["5. Exchange Rate"]);
    const points = extractCryptoSeriesPoints(dailyBlock, selectedCurrency);

    let change = 0;
    if (points.length >= 2) {
        const last = points[points.length - 1];
        const prev = points[points.length - 2];
        if (prev !== 0) change = ((last - prev) / prev) * 100;
    }

    if (!Number.isFinite(price) || !points.length) return null;

    return {
        name: asset.name,
        code: asset.code,
        price,
        change,
        points
    };
}

async function fetchRealNews() {
    const tickers = "AAPL,TSLA,MSFT,CRYPTO:BTC,CRYPTO:ETH";
    const url = `https://www.alphavantage.co/query?function=NEWS_SENTIMENT&tickers=${encodeURIComponent(tickers)}&limit=6&apikey=${encodeURIComponent(API_KEY)}`;
    const response = await fetchJSON(url);
    const feed = Array.isArray(response.feed) ? response.feed : [];

    return feed.slice(0, 5).map((item) => ({
        source: item.source || "Alpha Vantage",
        time: timeAgoFromPublished(item.time_published),
        title: item.title || "Untitled story",
        summary: item.summary || "No summary available."
    }));
}

async function fetchJSON(url) {
    const response = await fetch(url);
    const data = await response.json();

    if (data.Note || data.Information || data["Error Message"]) {
        throw new Error(data.Note || data.Information || data["Error Message"]);
    }

    return data;
}

function extractDailySeriesPoints(seriesObj) {
    const rawValues = Object.entries(seriesObj)
        .slice(0, 8)
        .reverse()
        .map(([, values]) => Number(values["4. close"]))
        .filter((value) => Number.isFinite(value));

    return normalizeSeriesToSparkPoints(rawValues);
}

function extractCryptoSeriesPoints(seriesObj, marketCode) {
    const key = `4b. close (${marketCode})`;
    const fallbackKey = `4a. close (USD)`;

    const rawValues = Object.entries(seriesObj)
        .slice(0, 8)
        .reverse()
        .map(([, values]) => Number(values[key] ?? values[fallbackKey]))
        .filter((value) => Number.isFinite(value));

    return normalizeSeriesToSparkPoints(rawValues);
}

function normalizeSeriesToSparkPoints(values) {
    if (!values.length) return [];
    const min = Math.min(...values);
    const max = Math.max(...values);
    if (min === max) return values.map(() => 50);

    return values.map((value) => 20 + ((value - min) / (max - min)) * 60);
}

function renderMarketSection() {
    if (!marketData.length) {
        marketCardsElement.innerHTML = '<div class="loading-card">Loading market data…</div>';
        return;
    }

    marketCardsElement.innerHTML = "";
    marketModeLabel.textContent = marketSourceMode === "api" ? "Real API" : "Demo";
    marketModeLabel.style.background = marketSourceMode === "api" ? "rgba(0,210,106,0.12)" : "rgba(77,163,255,0.14)";
    marketModeLabel.style.color = marketSourceMode === "api" ? "var(--positive)" : "var(--graph-line)";

    marketData.forEach((item) => {
        const card = document.createElement("div");
        card.className = "market-card";

        const trendClass = item.change >= 0 ? "positive" : "negative";
        const sign = item.change >= 0 ? "+" : "";
        const endPoint = getSparklineLastPoint(item.points);
        const gradientColor1 = item.change >= 0 ? "#00d26a" : "#ff4d4f";
        const gradientColor2 = item.change >= 0 ? "#1aff8c" : "#ff2d2f";
        const gradientColor3 = item.change >= 0 ? "#6dffe0" : "#ff7a7c";
        const fillColor = item.change >= 0 ? "#00d26a" : "#ff3b3b";
        const dotColor = item.change >= 0 ? "#4dffb8" : "#ff6b6b";

        card.innerHTML = `
            <div class="market-card-top">
                <div>
                    <div class="market-card-name">${escapeHtml(item.name)}</div>
                    <div class="market-card-code">${escapeHtml(item.code)}</div>
                </div>
                <span class="mini-label">${item.change >= 0 ? "Bullish" : "Active"}</span>
            </div>

            <div class="market-price">${formatMarketNumber(item.price)}</div>
            <div class="market-change ${trendClass}">${sign}${item.change.toFixed(2)}%</div>

            <svg class="market-mini-chart" viewBox="0 0 100 54" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="gradient-${item.code}" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%" stop-color="${gradientColor1}" stop-opacity="0.25"></stop>
                        <stop offset="50%" stop-color="${gradientColor2}" stop-opacity="1"></stop>
                        <stop offset="100%" stop-color="${gradientColor3}" stop-opacity="0.7"></stop>
                    </linearGradient>

                    <linearGradient id="fill-${item.code}" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="${fillColor}" stop-opacity="0.22"></stop>
                        <stop offset="100%" stop-color="${fillColor}" stop-opacity="0"></stop>
                    </linearGradient>

                    <filter id="glow-${item.code}" x="-50%" y="-50%" width="200%" height="200%">
                        <feGaussianBlur stdDeviation="2.4" result="blur"></feGaussianBlur>
                        <feMerge>
                            <feMergeNode in="blur"></feMergeNode>
                            <feMergeNode in="SourceGraphic"></feMergeNode>
                        </feMerge>
                    </filter>
                </defs>

                <path class="sparkline-area" fill="url(#fill-${item.code})" d="${buildSparklineAreaPath(item.points)}"></path>
                <path class="sparkline-path" filter="url(#glow-${item.code})" stroke="url(#gradient-${item.code})" d="${buildSmoothSparklinePath(item.points)}"></path>
                <circle class="sparkline-end-dot" cx="${endPoint.x}" cy="${endPoint.y}" r="2.8" fill="${dotColor}"></circle>
            </svg>
        `;

        marketCardsElement.appendChild(card);
    });
}

function renderMovers() {
    moversCardsElement.innerHTML = "";
    const movers = [...marketData].sort((a, b) => Math.abs(b.change) - Math.abs(a.change)).slice(0, 5);

    movers.forEach((item) => {
        const card = document.createElement("div");
        card.className = "mover-card";
        const trendClass = item.change >= 0 ? "positive" : "negative";
        const sign = item.change >= 0 ? "+" : "";

        card.innerHTML = `
            <div class="mover-card-top">
                <div>
                    <div class="mover-name">${escapeHtml(item.name)}</div>
                    <div class="mover-symbol">${escapeHtml(item.code)}</div>
                </div>
                <span class="mini-label">${item.change >= 0 ? "Up" : "Down"}</span>
            </div>
            <div class="mover-price">${formatMarketNumber(item.price)}</div>
            <div class="mover-change ${trendClass}">${sign}${item.change.toFixed(2)}%</div>
        `;

        moversCardsElement.appendChild(card);
    });
}

function renderNews() {
    newsListElement.innerHTML = "";

    newsData.forEach((item, index) => {
        const card = document.createElement("button");
        card.type = "button";
        card.className = "news-card";

        card.innerHTML = `
            <div class="news-meta">
                <span>${escapeHtml(item.source || "Market News")}</span>
                <span>${escapeHtml(item.time || "Latest")}</span>
            </div>
            <div class="news-title">${escapeHtml(item.title)}</div>
            <div class="news-summary">${escapeHtml(limitText(item.summary || "No summary available.", 140))}</div>
        `;

        card.addEventListener("click", () => openNewsModal(newsData[index]));
        newsListElement.appendChild(card);
    });
}

function renderInsight() {
    if (!marketData.length) {
        marketInsightBox.innerHTML = `<div class="insight-title">QUICK TAKE</div><div class="insight-text">Waiting for market data…</div>`;
        return;
    }

    const positiveCount = marketData.filter((item) => item.change >= 0).length;
    const negativeCount = marketData.length - positiveCount;
    const strongest = [...marketData].sort((a, b) => Math.abs(b.change) - Math.abs(a.change))[0];
    const mood = positiveCount >= negativeCount
        ? "Market mood looks slightly positive today."
        : "Market mood looks mixed to cautious today.";

    marketInsightBox.innerHTML = `
        <div class="insight-title">QUICK TAKE</div>
        <div class="insight-text">
            ${mood} ${positiveCount} assets are up while ${negativeCount} are down.
            The biggest move on screen is <strong>${escapeHtml(strongest.name)}</strong>
            at <strong>${strongest.change >= 0 ? "+" : ""}${strongest.change.toFixed(2)}%</strong>.
            This is for market awareness only, not financial advice.
        </div>
    `;
}

function saveState() {
    try {
        localStorage.setItem("netWorthAccounts", JSON.stringify(accounts));
        localStorage.setItem("netWorthHistory", JSON.stringify(history));
        localStorage.setItem("selectedCurrency", selectedCurrency);
        localStorage.setItem("theme", isDarkMode ? "dark" : "light");
    } catch (error) {
        console.error("Unable to save state:", error);
    }
}

function loadJSON(key, fallback) {
    try {
        const raw = localStorage.getItem(key);
        return raw ? JSON.parse(raw) : structuredClone(fallback);
    } catch {
        return structuredClone(fallback);
    }
}

function loadValue(key, fallback) {
    try {
        return localStorage.getItem(key) || fallback;
    } catch {
        return fallback;
    }
}

function cryptoId() {
    return `id-${Math.random().toString(36).slice(2, 11)}`;
}

function daysAgo(count) {
    const date = new Date();
    date.setDate(date.getDate() - count);
    return date.toISOString();
}

function sleep(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}