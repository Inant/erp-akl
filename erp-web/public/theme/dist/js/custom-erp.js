function formatCurrency(bilangan) {
    var	number_string = bilangan.toString(),
	sisa 	= number_string.length % 3,
	rupiah 	= number_string.substr(0, sisa),
	ribuan 	= number_string.substr(sisa).match(/\d{3}/g);
		
    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    return rupiah;
}

function formatDate(jsDate) {
    let dd = jsDate.getDate();
    let mm = jsDate.getMonth() + 1;
    let y = jsDate.getFullYear();

    return someFormattedDate = y + '-' + ( mm < 10 ? ('0' + mm) : mm ) + '-' + ( dd < 10 ? ('0' + dd) : dd );
}

function formatDateID(jsDate) {
    let dd = jsDate.getDate();
    let mm = jsDate.getMonth() + 1;
    let y = jsDate.getFullYear();

    return someFormattedDate = ( dd < 10 ? ('0' + dd) : dd ) + '-' +  ( mm < 10 ? ('0' + mm) : mm ) + '-' +  y ;
}