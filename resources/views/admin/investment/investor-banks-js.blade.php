  <script>
      const bankData = @json(getBankListByRegion());

      $('#banking_region').on('change', function() {
          bankingRegionOnchange($(this).val());
      });

      function bankingRegionOnchange(region = null) {

          if (!region) {
              region = $('#banking_region').val();
          }

          let bankEn = $('#investor_bank_name');
          let bankAr = $('#investor_bank_name_arabic');

          bankEn.empty().append('<option value="">Select Bank</option>');
          bankAr.empty().append('<option value="">اختر البنك</option>');

          if (bankData[region]) {

              bankData[region].forEach(function(bank) {

                  bankEn.append(
                      $('<option>', {
                          value: bank.name,
                          text: bank.name
                      })
                  );

                  bankAr.append(
                      $('<option>', {
                          value: bank.arabic,
                          text: bank.arabic
                      })
                  );

              });
          }

          // set selected values (EDIT MODE)
          if (window.investorData) {
              if (window.investorData?.primary_bank) {

                  bankEn.val(window.investorData.primary_bank.investor_bank_name);
                  bankAr.val(window.investorData.primary_bank.investor_bank_name_arabic);
              }
          }


          bankEn.trigger('change.select2');
          bankAr.trigger('change.select2');
      }
      $('#investor_bank_name').on('change', function() {

          let selectedBank = $(this).val();

          let region = $('#banking_region').val();

          if (bankData[region]) {

              let matched = bankData[region].find(bank => bank.name === selectedBank);

              if (matched) {
                  $('#investor_bank_name_arabic')
                      .val(matched.arabic)
                      .trigger('change.select2');
              }
          }
      });
      $('#investor_bank_name_arabic').on('change', function() {

          let selectedArabic = $(this).val();

          let region = $('#banking_region').val();

          if (bankData[region]) {

              let matched = bankData[region].find(bank => bank.arabic === selectedArabic);

              if (matched) {
                  $('#investor_bank_name')
                      .val(matched.name)
                      .trigger('change.select2');
              }
          }
      });
  </script>
