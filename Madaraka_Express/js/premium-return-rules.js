/**
 * Enforce premium return-trip constraints for same-day travel.
 * - Return date cannot be earlier than the departure date.
 * - Same-day inter-county outbound hides inter-county return option.
 * - Same-day 3pm express outbound forces a 10pm express return.
 */
(function ($) {
  function parseDate(value) {
    if (!value) return null;

    if (value.indexOf('-') > -1) {
      const parts = value.split('-');
      if (parts.length === 3) {
        const year = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10) - 1;
        const day = parseInt(parts[2], 10);
        const parsed = new Date(year, month, day);
        return Number.isNaN(parsed.getTime()) ? null : parsed;
      }
    }

    if (value.indexOf('/') > -1) {
      const parts = value.split('/');
      if (parts.length === 3) {
        const month = parseInt(parts[0], 10) - 1;
        const day = parseInt(parts[1], 10);
        const year = parseInt(parts[2].length === 2 ? `20${parts[2]}` : parts[2], 10);
        const parsed = new Date(year, month, day);
        return Number.isNaN(parsed.getTime()) ? null : parsed;
      }
    }

    const parsed = new Date(value);
    return Number.isNaN(parsed.getTime()) ? null : parsed;
  }

  function isSameDay(dateA, dateB) {
    if (!dateA || !dateB) return false;
    return (
      dateA.getFullYear() === dateB.getFullYear() &&
      dateA.getMonth() === dateB.getMonth() &&
      dateA.getDate() === dateB.getDate()
    );
  }

  function applyPremiumReturnRules() {
    const $departureInput = $('#dateInputPremium');
    const $returnInput = $('#returnDateInput');
    const $returnType = $('#return_train_type');
    const $returnTime = $('#return_depature_time');

    if (!$departureInput.length || !$returnInput.length || !$returnType.length || !$returnTime.length) {
      return;
    }

    const departureDateVal = $departureInput.val();
    let returnDateVal = $returnInput.val();
    const departureDate = parseDate(departureDateVal);
    let returnDate = parseDate(returnDateVal);

    if (departureDate && typeof $.fn.datepicker === 'function') {
      $returnInput.datepicker('option', 'minDate', departureDate);
    }

    if (departureDate && returnDate && returnDate < departureDate) {
      $returnInput.val(departureDateVal);
      returnDateVal = departureDateVal;
      returnDate = departureDate;
    }

    const sameDay = isSameDay(departureDate, returnDate);
    const outboundType = $('#premium_train_type').val();
    const outboundTime = ($('#premium_depature_time').val() || '').trim();

    const $interCountyOption = $returnType.find('option[value="inter_county"]');
    const $expressOption = $returnType.find('option[value="express"]');
    const $threePmOption = $returnTime.find('option[value="3:00 PM"]');
    const $tenPmOption = $returnTime.find('option[value="10:00 PM"]');

    let forceExpressReturn = false;
    let requireTenPmExpress = false;

    // Reset visibility before applying rules
    $interCountyOption.prop('disabled', false).show();
    $expressOption.prop('disabled', false).show();
    $threePmOption.prop('disabled', false).show();
    $tenPmOption.prop('disabled', false).show();

    // Same-day inter-county outbound: no inter-county return option.
    if (sameDay && outboundType === 'inter_county') {
      forceExpressReturn = true;
      $interCountyOption.prop('disabled', true).hide();
    }

    // Same-day 3pm express outbound: only 10pm express return.
    if (sameDay && outboundType === 'express' && outboundTime === '3:00 PM') {
      forceExpressReturn = true;
      requireTenPmExpress = true;
      $interCountyOption.prop('disabled', true).hide();
    }

    if (forceExpressReturn && $returnType.val() !== 'express') {
      $returnType.val('express');
      if (typeof fetch_premium_return_time === 'function') {
        fetch_premium_return_time('express');
      }
    }

    if (requireTenPmExpress) {
      $('#show_premium_return_time').removeClass('hide_data');
      $returnTime.prop('required', true);
      $threePmOption.prop('disabled', true).hide();
      $tenPmOption.prop('disabled', false).show();
      $returnTime.val('10:00 PM');
    }
  }

  function bindPremiumReturnRules() {
    const $premiumForm = $('#premium_train_type');
    const $returnSection = $('#return_trip_section');
    if (!$premiumForm.length || !$returnSection.length) {
      return;
    }

    $('#dateInputPremium, #returnDateInput, #premium_train_type, #premium_depature_time, #return_train_type').on(
      'change',
      applyPremiumReturnRules
    );

    applyPremiumReturnRules();
  }

  $(document).ready(bindPremiumReturnRules);

  // Expose for manual re-application if needed elsewhere.
  window.applyPremiumReturnRules = applyPremiumReturnRules;
})(jQuery);
