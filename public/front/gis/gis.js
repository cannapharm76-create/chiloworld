/* Medical Cannabis GIS map - same behaviour as the original dashboard, served from this site's API */
document.addEventListener("DOMContentLoaded", async function () {
  const API = (window.SITE ? window.SITE.root : "/") + "api/geo/";
  const endpoints = {
    markers: API + "markers",
    regions: API + "health-regions",
    provinces: API + "provinces",
    summary: API + "summary",
    trackDashboardView: API + "track-dashboard-view",
    autocomplete: API + "autocomplete",
  };

  const map = L.map("map", { minZoom: 6, zoomControl: false }).setView([13.7563, 100.5018], 6);

  // zoom buttons bottom-left
  L.control.zoom({ position: "bottomleft" }).addTo(map);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 18,
    attribution: "&copy; OpenStreetMap contributors"
  }).addTo(map);

  map.on("popupopen", function (event) {
    const popupElement = event.popup.getElement();
    if (!popupElement) return;
    const popupContent = popupElement.querySelector(".leaflet-popup-content");
    if (!popupContent) return;
    L.DomEvent.disableClickPropagation(popupContent);
    L.DomEvent.disableScrollPropagation(popupContent);
  });

  const markersLayer = L.layerGroup().addTo(map);

  const qInput = document.getElementById("q");
  const typeSelect = document.getElementById("type");
  const statusSelect = document.getElementById("status");
  const regionSelect = document.getElementById("health_region_id");
  const provinceSelect = document.getElementById("province_id");
  const searchForm = document.getElementById("search-form");
  const autocompleteList = document.getElementById("autocomplete-list");
  const loading = document.getElementById("gis-loading");

  let autocompleteTimer = null;
  let autocompleteAbort = null;
  let autocompleteItems = [];
  let autocompleteActiveIndex = -1;
  const autocompleteCache = new Map();

  function escapeHtml(value) {
    return String(value ?? "")
      .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
  }

  function escapeRegExp(value) {
    return String(value).replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
  }

  function highlightKeyword(text, keyword) {
    const safeText = escapeHtml(text || "");
    const safeKeyword = escapeRegExp(escapeHtml(keyword || ""));
    if (!safeKeyword) return safeText;
    return safeText.replace(new RegExp(`(${safeKeyword})`, "gi"), `<span class="autocomplete-highlight">$1</span>`);
  }

  function autocompleteCacheKey(keyword) {
    return [keyword, typeSelect.value || "all", statusSelect.value || "all", regionSelect.value || "", provinceSelect.value || ""].join("|");
  }

  function hideAutocomplete() {
    autocompleteList.innerHTML = "";
    autocompleteList.style.display = "none";
    autocompleteItems = [];
    autocompleteActiveIndex = -1;
  }

  function setAutocompleteLoading() {
    autocompleteList.innerHTML = `<div class="autocomplete-loading">กำลังค้นหา...</div>`;
    autocompleteList.style.display = "block";
  }

  function renderAutocomplete(items, keyword) {
    autocompleteList.innerHTML = "";
    autocompleteItems = items || [];
    autocompleteActiveIndex = -1;

    if (!autocompleteItems.length) {
      autocompleteList.innerHTML = `<div class="autocomplete-empty">ไม่พบข้อมูลที่ตรงกับคำค้นหา</div>`;
      autocompleteList.style.display = "block";
      return;
    }

    autocompleteItems.forEach((item, index) => {
      const div = document.createElement("div");
      div.className = "autocomplete-item";
      div.dataset.index = index;
      const typeLabel = item.marker_type === "establishment" ? "สถานประกอบการ" : "แปลงปลูก";
      div.innerHTML = `<strong>${highlightKeyword(item.name, keyword)}</strong><span class="autocomplete-type">${escapeHtml(typeLabel)}</span>`;
      div.addEventListener("mousedown", async function (e) {
        e.preventDefault();
        await selectAutocompleteItem(index);
      });
      autocompleteList.appendChild(div);
    });

    autocompleteList.style.display = "block";
  }

  function updateAutocompleteActiveItem() {
    const items = autocompleteList.querySelectorAll(".autocomplete-item");
    items.forEach(item => item.classList.remove("active"));
    if (autocompleteActiveIndex >= 0 && items[autocompleteActiveIndex]) {
      items[autocompleteActiveIndex].classList.add("active");
      items[autocompleteActiveIndex].scrollIntoView({ block: "nearest" });
    }
  }

  async function selectAutocompleteItem(index) {
    const item = autocompleteItems[index];
    if (!item) return;
    qInput.value = (item.name || "").trim();
    hideAutocomplete();
    await refreshDashboard();
  }

  async function fetchAutocomplete(keyword) {
    const key = autocompleteCacheKey(keyword);
    if (autocompleteCache.has(key)) {
      renderAutocomplete(autocompleteCache.get(key), keyword);
      return;
    }

    if (autocompleteAbort) autocompleteAbort.abort();
    autocompleteAbort = new AbortController();

    const params = new URLSearchParams();
    params.set("q", keyword);
    if (typeSelect.value) params.set("type", typeSelect.value);
    if (statusSelect.value) params.set("status", statusSelect.value);
    if (regionSelect.value) params.set("health_region_id", regionSelect.value);
    if (provinceSelect.value) params.set("province_id", provinceSelect.value);

    setAutocompleteLoading();

    try {
      const res = await fetch(`${endpoints.autocomplete}?${params.toString()}`, { signal: autocompleteAbort.signal });
      if (!res.ok) { hideAutocomplete(); return; }
      const data = await res.json();
      autocompleteCache.set(key, data);
      renderAutocomplete(data, keyword);
    } catch (err) {
      if (err.name !== "AbortError") {
        console.error("autocomplete error:", err);
        hideAutocomplete();
      }
    }
  }

  function buildQuery() {
    const params = new URLSearchParams();
    if (qInput.value.trim()) params.set("q", qInput.value.trim());
    if (typeSelect.value) params.set("type", typeSelect.value);
    if (statusSelect.value) params.set("status", statusSelect.value);
    if (regionSelect.value) params.set("health_region_id", regionSelect.value);
    if (provinceSelect.value) params.set("province_id", provinceSelect.value);
    return params.toString();
  }

  function fmt(n) {
    return Number(n ?? 0).toLocaleString("th-TH");
  }

  function updateStats(summary) {
    document.getElementById("stat_total").textContent = fmt(summary.total);
    document.getElementById("stat_establishments").textContent = fmt(summary.establishments);
    document.getElementById("stat_farm_plots").textContent = fmt(summary.farm_plots);
    document.getElementById("stat_active").textContent = fmt(summary.active);
    document.getElementById("stat_suspended").textContent = fmt(summary.suspended);
    document.getElementById("stat_revoked").textContent = fmt(summary.revoked);
    document.getElementById("stat_dashboard_views").textContent = fmt(summary.dashboard_views);
  }

  async function trackDashboardViewOnce() {
    const storageKey = "dashboard_view_tracked";
    try { if (sessionStorage.getItem(storageKey) === "1") return; } catch (e) { /* storage blocked */ }
    try {
      const res = await fetch(endpoints.trackDashboardView, { method: "POST", headers: { "Content-Type": "application/json", "Accept": "application/json" } });
      if (res.ok) { try { sessionStorage.setItem(storageKey, "1"); } catch (e) { /* ignore */ } }
    } catch (err) {
      console.error("trackDashboardViewOnce error:", err);
    }
  }

  let refreshSeq = 0; // only the latest search may update the page

  async function loadSummary(seq) {
    const qs = buildQuery();
    const res = await fetch(qs ? `${endpoints.summary}?${qs}` : endpoints.summary);
    const data = await res.json();
    if (seq === refreshSeq) updateStats(data);
  }

  async function loadRegions() {
    const res = await fetch(endpoints.regions);
    const rows = await res.json();
    regionSelect.innerHTML = `<option value="">ทุกเขตสุขภาพ</option>`;
    rows.forEach(item => {
      const option = document.createElement("option");
      option.value = item.id;
      option.textContent = item.name_th;
      regionSelect.appendChild(option);
    });
  }

  async function loadProvinces(selectedId = "") {
    const params = new URLSearchParams();
    if (regionSelect.value) params.set("health_region_id", regionSelect.value);
    const res = await fetch(params.toString() ? `${endpoints.provinces}?${params.toString()}` : endpoints.provinces);
    const rows = await res.json();
    provinceSelect.innerHTML = `<option value="">ทุกจังหวัด</option>`;
    rows.forEach(item => {
      const option = document.createElement("option");
      option.value = item.id;
      option.textContent = item.name_th;
      if (String(item.id) === String(selectedId)) option.selected = true;
      provinceSelect.appendChild(option);
    });
  }

  function formatThaiDate(dateStr) {
    if (!dateStr) return "-";
    const date = new Date(dateStr);
    if (Number.isNaN(date.getTime())) return dateStr;
    const monthNames = ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
    return `${date.getDate()} ${monthNames[date.getMonth()]} ${date.getFullYear() + 543}`;
  }

  function normalizeCertifiedStandards(value) {
    if (!value) return [];
    if (Array.isArray(value)) return value.map(v => String(v).trim()).filter(v => v && v !== "-" && v !== "ไม่มี");
    if (typeof value === "string") return value.split(",").map(v => v.trim()).filter(v => v && v !== "-" && v !== "ไม่มี");
    return [];
  }

  function establishmentStatusLabel(status) {
    const labels = { active: "ดำเนินกิจการอยู่", closed: "ปิดกิจการ", suspended: "พักใช้ใบอนุญาต", revoked: "เพิกถอนใบอนุญาต" };
    return labels[status] || "-";
  }

  function popupHtml(item) {
    const typeLabel = item.marker_type === "establishment" ? "สถานประกอบการ" : "แปลงปลูก";
    const certifiedStandards = normalizeCertifiedStandards(item.certified_standards);
    const certifiedHtml = certifiedStandards.length ? certifiedStandards.map(v => `<li>${escapeHtml(v)}</li>`).join("") : "<li>-</li>";

    return `
      <div>
        <div class="fw-bold mb-1">${escapeHtml(item.name || "-")}</div>
        <div>ชนิดข้อมูล: ${escapeHtml(typeLabel)}</div>
        <div>ประเภท: ${escapeHtml(item.category || "-")}</div>
        <div>สถานะ : ${escapeHtml(establishmentStatusLabel(item.status))}</div>
        <div>เลขที่อ้างอิง: ${escapeHtml(item.license_no || "-")}</div>
        <div>จังหวัด: ${escapeHtml(item.province_name || "-")}</div>
        ${item.marker_type === "establishment"
          ? `<div>วันสิ้นอายุใบอนุญาต: ${formatThaiDate(item.license_expiry_date)}</div>`
          : `
            <div class="mt-2"><strong>มาตรฐานที่ได้รับการรับรอง</strong></div>
            <ul>${certifiedHtml}</ul>
            <div class="mt-2"><strong>ระบบการปลูก</strong></div>
            <ul>
              ${(item.is_indoor || item.is_greenhouse || item.is_outdoor)
                ? `
                  ${item.is_indoor ? `<li>Indoor: ${item.area_indoor ?? "-"} ตร.ม.</li>` : ""}
                  ${item.is_greenhouse ? `<li>Greenhouse: ${item.area_greenhouse ?? "-"} ตร.ม.</li>` : ""}
                  ${item.is_outdoor ? `<li>Outdoor: ${item.area_outdoor ?? "-"} ตร.ม.</li>` : ""}
                `
                : `<li>-</li>`}
            </ul>
          `}
      </div>
    `;
  }

  function getEstablishmentColor(item) {
    const status = String(item.status || "").toLowerCase();
    if (status === "revoked") return "black";      // เพิกถอน
    if (status === "suspended") return "gray";     // พักใช้
    if (status === "closed") return "white";       // ปิดกิจการ

    const expiry = item.license_expiry_date ? new Date(item.license_expiry_date) : null;
    if (!expiry || Number.isNaN(expiry.getTime())) return "white";
    if (expiry <= new Date("2026-12-31")) return "red";
    if (expiry >= new Date("2027-01-01") && expiry <= new Date("2027-12-31")) return "yellow";
    if (expiry >= new Date("2028-01-01") && expiry <= new Date("2028-12-31")) return "green";
    return "white";
  }

  function createEstablishmentIcon(item) {
    return L.divIcon({
      className: "custom-est-marker",
      html: `<div class="est-marker-pin ${getEstablishmentColor(item)}"></div>`,
      iconSize: [16, 16],
      iconAnchor: [8, 16],
      popupAnchor: [0, -16]
    });
  }

  function createFarmPlotIcon() {
    return L.divIcon({
      className: "custom-farm-marker",
      html: `<div class="farm-marker-tree">🌳</div>`,
      iconSize: [18, 18],
      iconAnchor: [9, 18],
      popupAnchor: [0, -18]
    });
  }

  function getMarkerIcon(item) {
    return item.marker_type === "farm_plot" ? createFarmPlotIcon() : createEstablishmentIcon(item);
  }

  async function loadMarkers(seq) {
    const qs = buildQuery();
    const res = await fetch(qs ? `${endpoints.markers}?${qs}` : endpoints.markers);
    const rows = await res.json();
    if (seq !== refreshSeq) return;
    markersLayer.clearLayers();
    const bounds = [];

    rows.forEach(item => {
      if (item.lat == null || item.lng == null) return;
      const marker = L.marker([item.lat, item.lng], { icon: getMarkerIcon(item) });
      marker.bindPopup(popupHtml(item), { autoPan: true, keepInView: true, maxWidth: 350 });
      marker.addTo(markersLayer);
      bounds.push([item.lat, item.lng]);
    });

    if (bounds.length > 0) {
      map.fitBounds(bounds, { padding: [30, 30], maxZoom: 12 });
    } else {
      map.setView([13.7563, 100.5018], 6);
    }
  }

  async function refreshDashboard() {
    const seq = ++refreshSeq;
    loading.classList.add("show");
    try {
      await Promise.all([loadSummary(seq), loadMarkers(seq)]);
    } catch (err) {
      console.error("refreshDashboard error:", err);
    } finally {
      if (seq === refreshSeq) loading.classList.remove("show");
    }
  }

  qInput.addEventListener("input", function () {
    clearTimeout(autocompleteTimer);
    const keyword = qInput.value.trim();
    if (keyword.length < 3) { hideAutocomplete(); return; }
    autocompleteTimer = setTimeout(function () { fetchAutocomplete(keyword); }, 300);
  });

  qInput.addEventListener("keydown", async function (e) {
    if (autocompleteList.style.display !== "block") return;
    if (e.key === "ArrowDown") {
      e.preventDefault();
      autocompleteActiveIndex = Math.min(autocompleteActiveIndex + 1, autocompleteItems.length - 1);
      updateAutocompleteActiveItem();
    }
    if (e.key === "ArrowUp") {
      e.preventDefault();
      autocompleteActiveIndex = Math.max(autocompleteActiveIndex - 1, 0);
      updateAutocompleteActiveItem();
    }
    if (e.key === "Enter" && autocompleteActiveIndex >= 0) {
      e.preventDefault();
      await selectAutocompleteItem(autocompleteActiveIndex);
    }
    if (e.key === "Escape") hideAutocomplete();
  });

  document.addEventListener("click", function (e) {
    if (!e.target.closest(".autocomplete-wrap")) hideAutocomplete();
  });

  regionSelect.addEventListener("change", async function () {
    autocompleteCache.clear();
    hideAutocomplete();
    await loadProvinces();
    await refreshDashboard();
  });

  [provinceSelect, typeSelect, statusSelect].forEach(function (el) {
    el.addEventListener("change", async function () {
      autocompleteCache.clear();
      hideAutocomplete();
      await refreshDashboard();
    });
  });

  searchForm.addEventListener("submit", async function (e) {
    e.preventDefault();
    hideAutocomplete();
    await refreshDashboard();
  });

  const legend = L.control({ position: "bottomright" });
  legend.onAdd = function () {
    const div = L.DomUtil.create("div", "map-legend");
    div.innerHTML = `
      <div class="legend-item"><span class="legend-tree">🌳</span><span>แปลงปลูก</span></div>
      <div class="legend-item"><span class="legend-dot" style="background:#c42923;"></span><span>สถานประกอบการ : สิ้นอายุปี 2569</span></div>
      <div class="legend-item"><span class="legend-dot" style="background:#f1c40f;"></span><span>สถานประกอบการ : สิ้นอายุปี 2570</span></div>
      <div class="legend-item"><span class="legend-dot" style="background:#198149;"></span><span>สถานประกอบการ : สิ้นอายุปี 2571</span></div>
      <div class="legend-item"><span class="legend-dot" style="background:#ffffff;border-color:#7f8c8d;"></span><span>อื่น ๆ / ไม่มีข้อมูล</span></div>
      <div class="legend-item"><span class="legend-dot" style="background:#95a5a6;"></span><span>พักใช้ใบอนุญาต</span></div>
      <div class="legend-item"><span class="legend-dot" style="background:#111111;"></span><span>เพิกถอนใบอนุญาต</span></div>
    `;
    return div;
  };
  legend.addTo(map);

  await trackDashboardViewOnce();
  await loadRegions();
  await loadProvinces();
  await refreshDashboard();
});
