// Example function for handling filter modal and applying filters
function applyFilters() {
    var selectedMonth = $('#monthSelect').val();
    var selectedYearRange = $('#yearRangeDropdown .dropdown-item.active').data('year-range');
    var url = new URL(window.location.href);
    url.searchParams.set('month', selectedMonth);
    url.searchParams.set('year_range', selectedYearRange);
    window.location.href = url.toString();
}
