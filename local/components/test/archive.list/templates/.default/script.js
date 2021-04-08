/* 
 Comment Text 
*/

 window.onload = function() {
//     console.log("Year", arYear);
//     let SelectYearMagazine = document.getElementById('yearMagazine');
//     SelectYearMagazine.onchange = function(){
//         let selectedValue = SelectYearMagazine.options[SelectYearMagazine.selectedIndex].value;
//         console.log(selectedValue);
//     };
// }

const selectBox = {
    init: function() {
      const numbers = {
        1: [
          {
            'name': '172',
            'value': '172'
          },
          {
            'name': '173',
            'value': '173'
          }
        ],
        2: [
          {
            'name': '174',
            'value': '174'
          },
          {
            'name': '175',
            'value': '175'
          }
        ],
        3: [
          {
            'name': '176',
            'value': '176'
          },
          {
            'name': '177',
            'value': '177'
          }
        ]
      };
      function selectCallback(val, index) {
        if ($(this).find('select').attr('name') === 'year') {
          if (val !== '') {
            var select = $('#number');
            if (select.length) {
              select.closest('.selecter').find('.selecter-options').scroller('destroy');
              select.empty().append($("<option selected='selected' value=''>Выпуск</option>")).val('');
              $.each(numbers[index], function(key, text) {
                select.append($('<option></option>').attr('value', text.value).text(text.name));
              });
              select.selecter('update');
              select.closest('.selecter').find('.selecter-options').scroller();
              select.selecter('enable');
            } else {
              select.empty().append($("<option selected='selected' value=''>Выпуск</option>")).val('');
              select.selecter('update');
              select.closest('.selecter').find('.selecter-options').scroller('destroy');
              select.closest('.selecter').find('.selecter-options').scroller();
              select.selecter('disable');
            }
          }
        }
      }
  
      if ($('.select-box').length) {
        $('.select-box').selecter({
          customClass: 'selecter_custom scroll-select',
          handleSize: 60,
          callback: selectCallback,
          mobile: true
        });
      }
    }
  };
  
  document.addEventListener('DOMContentLoaded', function() {
    selectBox.init();
  })
  
}