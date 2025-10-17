function error_text(field, rules){
    if (rules == 'required') {
        var err_text = field + " Harus Diisi !"
    }else if (rules == 'number') {
        var err_text = field + " Harus Angka !"
    }else if (rules == 'format') {
        var err_text = field + " Format Salah !"
    }else if (rules == 'periode') {
        var err_text = field + " Harus Lebih Besar Dari Periode 1"
    }else if (rules == 'custom') {
        var err_text = field
    }
    swal({
       title: "Warning",
       text: ""+ err_text,
       icon: "warning",
       // buttons: true,
       dangerMode: true,
    }).then(function(){
      return false;
    }) 
}

function format_date(userdate)
{
    var date    = new Date(userdate),
    yr      = date.getFullYear(),
    month   = date.getMonth() < 10 ? '0' + date.getMonth() : date.getMonth(),
    day     = date.getDate()  < 10 ? '0' + date.getDate()  : date.getDate(),
    newDate = yr + '-' + month + '-' + day;
    console.log(newDate);
}