/*
Autofill Extension for Google Chrome:
https://chromewebstore.google.com/detail/autofill/nlmmgnhgdeffjkdckmikfpnddkbbfkkk

Filter Site:
https://everify.bdris.gov.bd/

This script can be used for verify birth certificate.
*/

// update 2024-01-18

if(document.getElementsByTagName('td').length>30){
  
  const monthNames = ["","January","February","March","April","May","June","July","August","September","October","November","December"];
  var monthZ = document.getElementsByTagName('td')[10].innerHTML;
  
  for(let month = 1; month < 13; month++) { if(monthNames[month]==monthZ.split(' ')[1]) {if(month<10) month = '0'+month; monthZ = monthZ.split(' ')[0]+'/'+month+'/'+monthZ.split(' ')[2]; } }
  
  document.getElementsByTagName('td')[10].innerHTML = "<form Xtarget='_blank' action='https://example.com/admissionX.php' method=post id=infoB ><input type=hidden name='data_personNameEn' value='"+document.getElementsByTagName('td')[16].innerHTML+"'><input type=hidden name='data_personNameBn' value='"+document.getElementsByTagName('td')[14].innerHTML+"'><input type=hidden name='data_fatherNameEn' value='"+document.getElementsByTagName('td')[33].innerHTML+"'><input type=hidden name='data_fatherNameBn' value='"+document.getElementsByTagName('td')[31].innerHTML+"'><input type=hidden name='data_motherNameEn' value='"+document.getElementsByTagName('td')[25].innerHTML+"'><input type=hidden name='data_motherNameBn' value='"+document.getElementsByTagName('td')[23].innerHTML+"'><input type=hidden name='data_officeAddressEn' value='"+document.getElementsByTagName('td')[21].innerHTML+"'><input type=hidden name='data_personDob' value='"+monthZ+"'><input type=hidden name='data_ubrn' value='"+document.getElementsByTagName('b')[0].innerHTML+"'><input type=hidden name='data_gender' value='"+document.getElementsByTagName('td')[12].innerHTML+"'><input type=submit value='"+document.getElementsByTagName('td')[10].innerHTML+"' ></form>";
  
  infoB.submit();
  
}



if(location.href.split('#').length==3){
  
  ubrn.value=location.href.split('#')[1];
  BirthDate.value=location.href.split('#')[2];
  CaptchaInputText.outerHTML = "<input autocomplete=off id=hutash onkeyup='CaptchaInputText.value=eval(this.value);' style='width: 70px;' > = <input autocomplete=off id=CaptchaInputText name=CaptchaInputText type=text  style='width: 70px;' >";
  const captchaImage = document.getElementById('CaptchaImage');
  
  captchaImage.style.width = '300px';
  captchaImage.style.overflow = 'hidden';
  captchaImage.style.clipPath = 'inset(0 80px 0 0px)';
  captchaImage.style.mixBlendMode = 'difference';
  hutash.focus();

}
