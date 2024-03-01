/**
 * DjoSmer, 2024.02
 */

$(function () {
  const token = $('#token').text();

  const storageKey = 'lancacheCacheDomainParams';
  const cacheDomainDefaultParams = {
    url: 'https://raw.githubusercontent.com/uklans/cache-domains/master/cache_domains.json',
    ip: ''
  }

  const paramsString = localStorage.getItem(storageKey);
  const params = paramsString !== null ? JSON.parse(paramsString) : cacheDomainDefaultParams;

  $('#cacheDomainsUrl').val(utils.unescapeHtml(params.url));
  $('#lancacheIP').val(utils.unescapeHtml(params.ip));

  const btnUpdateCacheDomains = document.querySelector('#btnUpdateCacheDomains');
  const elAllCheckboxes = document.querySelector('#status_allCheckboxes');

  elAllCheckboxes.addEventListener('change', function () {
    document.querySelectorAll('.cacheDomainCheckboxes').forEach((el) => {
      el.checked = this.checked;
    });
  });

  $('#btnGetCacheDomains').on('click', function () {
    btnUpdateCacheDomains.disabled = false;
    const url = utils.escapeHtml($('#cacheDomainsUrl').val());
    saveParams({...params, url})

    $('#listCacheDomains').DataTable({
      ajax: {
        url: 'scripts/pi-hole/php/lancache/actions.php',
        data: {action: "get", token, url},
        type: "POST",
      },
      columns: [
        {
          data: 'name',
          render: function (data, type, row) {
            if (type === 'display') {
              const id = `${row.name}_checkbox`
              return `<input type="checkbox" id="${id}" checked class="cacheDomainCheckboxes" data-row="${encodeURIComponent(JSON.stringify(row))}"/>`;
            }
            return data;
          },
        },
        {
          data: 'name'
        },
        {
          data: 'description'
        }
      ],
      lengthMenu: [
        [-1],
      ],
      ordering: false,
      searching: false,
      lengthChange: false,
      paging: false,
      order: [[1, 'asc']],
      stateDuration: 0,
      createdRow: function (row, data, dataIndex) {
        row.style.cursor = 'pointer';
        const elCheckbox = row.querySelector('input[type=checkbox]');
        row.addEventListener('click', function (e) {
          if (e.target.tagName !== 'INPUT') {
            elCheckbox.checked = (!elCheckbox.checked);
          }

          const countChecked = document.querySelectorAll('.cacheDomainCheckboxes:checked').length;
          const countUnchecked = document.querySelectorAll('.cacheDomainCheckboxes:not(:checked)').length;

          elAllCheckboxes.checked = countChecked && !countUnchecked;
          elAllCheckboxes.indeterminate = countChecked && countUnchecked;

        });
      },
      drawCallback: () => {
        elAllCheckboxes.checked = true;
        elAllCheckboxes.indeterminate = false;
      }
    });
  });

  const ta = $('#output');

  $(btnUpdateCacheDomains).on('click', function () {
    this.disabled = true;

    const domains = [];
    document.querySelectorAll('.cacheDomainCheckboxes:checked').forEach((el) => {
      const data = JSON.parse(decodeURIComponent(el.dataset.row));
      domains.push(data.name);
    });

    const ip = utils.escapeHtml($('#lancacheIP').val());
    saveParams({...params, ip})

    $('#outputModal').modal('toggle');

    if (typeof EventSource !== 'function') {
      ta.html("Updating lists of cache domains is not supported with this browser!");
      return;
    }

    const source = new EventSource(`scripts/pi-hole/php/lancache/actions.php?action=update&domains=${domains.join(',')}&ip=${ip}`);

    ta.html('');

    source.addEventListener(
      'message',
      function (e) {
        const data = JSON.parse(e.data);

        if (data.message[0] !== '-') {
          ta.append("\n");
        }
        ta.append(data.message + "\n");

        if (data.success !== undefined) {
          btnUpdateCacheDomains.disabled = false;
          source.close();
          ta.append('Closed.');
        }
      },
      false
    );

    source.addEventListener(
      'error',
      function () {
        source.close();
        btnUpdateCacheDomains.disabled = false;
      },
      false
    );

  });

  function saveParams(params) {
    localStorage.setItem(storageKey, JSON.stringify(params));
  }
});
