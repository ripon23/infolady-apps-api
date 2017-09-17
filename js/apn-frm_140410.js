/* 
 * Description of apn-frm.js
 *
 * @author Rafiqul Islam <rafiqul.islam@dnet.org.bd>
 */


// fix autofocus for internet explorer
$(function() {
  $('[autofocus]:not(:focus)').eq(0).focus();
});


$('.numbersOnly').keyup(function () { 
    this.value = this.value.replace(/[^0-9\.]/g,'');
});    
    
    
$('#mb_date_1').autotab({ format: 'number', target: '#mb_date_2' });
$('#mb_date_2').autotab({ format: 'number', target: '#mb_date_3', previous: '#mb_date_1' });
$('#mb_date_3').autotab({ format: 'number', target: '#mb_date_4', previous: '#mb_date_2' });
$('#mb_date_4').autotab({ format: 'number', target: '#mb_date_5', previous: '#mb_date_3' });
$('#mb_date_5').autotab({ format: 'number', target: '#mb_date_6', previous: '#mb_date_4' });
$('#mb_date_6').autotab({ format: 'number', target: '#mb_date_7', previous: '#mb_date_5' });
$('#mb_date_7').autotab({ format: 'number', target: '#mb_date_8', previous: '#mb_date_6' });
$('#mb_date_8').autotab({ format: 'number', target: '#subs_mobile_1', previous: '#mb_date_7' });


$('#subs_mobile_1').autotab({ format: 'number', target: '#subs_mobile_2' });
$('#subs_mobile_2').autotab({ format: 'number', target: '#subs_mobile_3', previous: '#subs_mobile_1' });
$('#subs_mobile_3').autotab({ format: 'number', target: '#subs_mobile_4', previous: '#subs_mobile_2' });
$('#subs_mobile_4').autotab({ format: 'number', target: '#subs_mobile_5', previous: '#subs_mobile_3' });
$('#subs_mobile_5').autotab({ format: 'number', target: '#subs_mobile_6', previous: '#subs_mobile_4' });
$('#subs_mobile_6').autotab({ format: 'number', target: '#subs_mobile_7', previous: '#subs_mobile_5' });
$('#subs_mobile_7').autotab({ format: 'number', target: '#subs_mobile_8', previous: '#subs_mobile_6' });
$('#subs_mobile_8').autotab({ format: 'number', target: '#subs_mobile_9', previous: '#subs_mobile_7' });
$('#subs_mobile_9').autotab({ format: 'number', target: '#subs_mobile_10', previous: '#subs_mobile_8' });
$('#subs_mobile_10').autotab({ format: 'number', target: '#subs_mobile_11', previous: '#subs_mobile_9' });
$('#subs_mobile_11').autotab({ format: 'number', target: '#service_model', previous: '#subs_mobile_10' });


$('#fam_mem_mobile_1').autotab({ format: 'number', target: '#fam_mem_mobile_2' });
$('#fam_mem_mobile_2').autotab({ format: 'number', target: '#fam_mem_mobile_3', previous: '#fam_mem_mobile_1' });
$('#fam_mem_mobile_3').autotab({ format: 'number', target: '#fam_mem_mobile_4', previous: '#fam_mem_mobile_2' });
$('#fam_mem_mobile_4').autotab({ format: 'number', target: '#fam_mem_mobile_5', previous: '#fam_mem_mobile_3' });
$('#fam_mem_mobile_5').autotab({ format: 'number', target: '#fam_mem_mobile_6', previous: '#fam_mem_mobile_4' });
$('#fam_mem_mobile_6').autotab({ format: 'number', target: '#fam_mem_mobile_7', previous: '#fam_mem_mobile_5' });
$('#fam_mem_mobile_7').autotab({ format: 'number', target: '#fam_mem_mobile_8', previous: '#fam_mem_mobile_6' });
$('#fam_mem_mobile_8').autotab({ format: 'number', target: '#fam_mem_mobile_9', previous: '#fam_mem_mobile_7' });
$('#fam_mem_mobile_9').autotab({ format: 'number', target: '#fam_mem_mobile_10', previous: '#fam_mem_mobile_8' });
$('#fam_mem_mobile_10').autotab({ format: 'number', target: '#fam_mem_mobile_11', previous: '#fam_mem_mobile_9' });
$('#fam_mem_mobile_11').autotab({ format: 'number', target: '#fam_mem_relation', previous: '#fam_mem_mobile_10' });


// blur | keyup
$('.rad_choose').on('blur', function() {
	if( $(this).val() ) {
	
		var txt_inpt = $(this).attr('id');
		var rad_inpt = txt_inpt + '_rad'+ $(this).val();
		
		if ( $('#'+ rad_inpt ).length)
		{		
			$('#'+ rad_inpt).attr('checked', true);
            
            configSubscriberEducation(rad_inpt);
            configFamilyMemberRcvInfo(rad_inpt);
            configServicemodel(rad_inpt);
		}
		else {
			alert( 'Wrong input!');
            
			setTimeout( function() { $('#' + txt_inpt).focus().select() }, 0 );
            //$( '#' + txt_inpt ).focus().select();
		}
	}
	
});

$("input:radio[name=subs_ed_rad]").change(function() {
    var rad_inpt = $(this).attr('id');
    var int_val = $(this).val()==='YES' ? '1' : '2';
    $('#subs_ed').val(int_val);
    configSubscriberEducation(rad_inpt);
});

$("input:radio[name=fam_mem_rcv_inf_w_rad]").change(function() {
    var rad_inpt = $(this).attr('id');
    var int_val = $(this).val()==='YES' ? '1' : '2';
    $('#fam_mem_rcv_inf_w').val(int_val);
    configFamilyMemberRcvInfo(rad_inpt);
});

$("input:radio[name=service_model_rad]").change(function() {
    var rad_inpt = $(this).attr('id');
    var inpt = $(this).val();
    var int_val = '';
    
    if(inpt === 's') {
        int_val = '1';
    }
    else if(inpt === 'd') {
        int_val = '2';
    }
    else if(inpt === 'r') {
        int_val = '3';
    }
    
    $('#service_model').val(int_val);
    configServicemodel(rad_inpt);
});


$('#mb_date_8').blur(function() {
	var d1 = $('#mb_date_1').val();
	var d2 = $('#mb_date_2').val();
	var m1 = $('#mb_date_3').val();
	var m2 = $('#mb_date_4').val();
	var y1 = $('#mb_date_5').val();
	var y2 = $('#mb_date_6').val();
	var y3 = $('#mb_date_7').val();
	var y4 = $('#mb_date_8').val();
	
	if( !isValidDate(d1,d2,m1,m2,y1,y2,y3,y4) ) {
        alert('Invalid date!');
        //$('#mb_date_1').focus();
        setTimeout( function() { $('#mb_date_1').focus().select() }, 0 );
	}
});


$('#same_subs_mobile_chk').change(function() {
   if($(this).is(":checked")) {

        $('#fam_mem_mobile_1').val($('#subs_mobile_1').val());
        $('#fam_mem_mobile_2').val($('#subs_mobile_2').val());
        $('#fam_mem_mobile_3').val($('#subs_mobile_3').val());
        $('#fam_mem_mobile_4').val($('#subs_mobile_4').val());
        $('#fam_mem_mobile_5').val($('#subs_mobile_5').val());
        $('#fam_mem_mobile_6').val($('#subs_mobile_6').val());
        $('#fam_mem_mobile_7').val($('#subs_mobile_7').val());
        $('#fam_mem_mobile_8').val($('#subs_mobile_8').val());
        $('#fam_mem_mobile_9').val($('#subs_mobile_9').val());
        $('#fam_mem_mobile_10').val($('#subs_mobile_10').val());
        $('#fam_mem_mobile_11').val($('#subs_mobile_11').val());
        
        //$('#fam_mem_relation').focus();
        setTimeout( function() { $('#fam_mem_relation').focus().select() }, 0 );

   }
   else{
        $('#fam_mem_mobile_1').val('');
        $('#fam_mem_mobile_2').val('');
        $('#fam_mem_mobile_3').val('');
        $('#fam_mem_mobile_4').val('');
        $('#fam_mem_mobile_5').val('');
        $('#fam_mem_mobile_6').val('');
        $('#fam_mem_mobile_7').val('');
        $('#fam_mem_mobile_8').val('');
        $('#fam_mem_mobile_9').val('');
        $('#fam_mem_mobile_10').val('');
        $('#fam_mem_mobile_11').val('');
        
        //$('#fam_mem_mobile_1').focus();
        setTimeout( function() { $('#fam_mem_mobile_1').focus().select() }, 0 );
   }
});



function isValidDate(d1,d2,m1,m2,y1,y2,y3,y4)
{
    var d = parseInt(d1+d2);
    var m = parseInt(m1+m2) - 1;
    var y = parseInt(y1+y2+y3+y4);
    var composedDate = new Date(y, m, d);
    return  composedDate.getDate() == d &&
            composedDate.getMonth() == m &&
            composedDate.getFullYear() == y &&
			y > 1950 
        ;
}


function configSubscriberEducation(rad_inpt) 
{
    if(rad_inpt === 'subs_ed_rad1') {
       $(".edu-n").removeClass("edu-n-disp");
       //$('#subs_ed_yr').focus();
       setTimeout( function() { $('#subs_ed_yr').focus().select() }, 0 );
    }
    else if(rad_inpt === 'subs_ed_rad2') {
       $(".edu-n").addClass("edu-n-disp");
       //$('#monthly_income').focus();
       setTimeout( function() { $('#monthly_income').focus().select() }, 0 );
    }
}


function configFamilyMemberRcvInfo(rad_inpt) 
{
    if(rad_inpt === 'fam_mem_rcv_inf_w_rad1') {
       $(".fmri-n").removeClass("fmri-n-disp");
       //$('#fam_mem_mobile_1').focus();
       setTimeout( function() { $('#fam_mem_mobile_1').focus().select() }, 0 );
    }
    else if(rad_inpt === 'fam_mem_rcv_inf_w_rad2') {
       $(".fmri-n").addClass("fmri-n-disp");
       //$('#tot_home_phone').focus();
       setTimeout( function() { $('#tot_home_phone').focus().select() }, 0 );
    }
}


function configServicemodel(rad_inpt) 
{
    if(rad_inpt.indexOf("service_model_rad") >= 0){
        if($( "#"+rad_inpt+":checked" ).val() === 'r') {
           $(".svc-mdl").removeClass("svc-mdl-disp");
           //$('#srvc_schd').focus();
           setTimeout( function() { $('#srvc_schd').focus().select() }, 0 );
        }
        else {
           $(".svc-mdl").addClass("svc-mdl-disp");
           //$('#fam_mem_rcv_inf_w').focus();
           setTimeout( function() { $('#fam_mem_rcv_inf_w').focus().select() }, 0 );
        }
    }
//    if(rad_inpt === 'service_model_rad3') {
//       $(".svc-mdl").removeClass("svc-mdl-disp");
//       //$('#srvc_schd').focus();
//       setTimeout( function() { $('#srvc_schd').focus().select() }, 0 );
//    }
//    else if(rad_inpt === 'service_model_rad1' || rad_inpt === 'service_model_rad2') {
//       $(".svc-mdl").addClass("svc-mdl-disp");
//       //$('#fam_mem_rcv_inf_w').focus();
//       setTimeout( function() { $('#fam_mem_rcv_inf_w').focus().select() }, 0 );
//    }
}