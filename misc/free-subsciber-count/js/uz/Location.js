/**
 * 
 * @author A N M Mahabubul Hasan
 */
var Location = {
    division: null,
    district: null,
    upazila: null,
    union: null,
    supervisor:null,
    selPartner:null,
    partner:null,
    
    
    init: function () {
        var that = this;
        that.division = $('#selDivision');
        that.district = $('#selDistrict');
        that.upazila = $('#selUpazila');
        that.union = $('#selUnion');
        that.supervisor =$('#supervisor');
        that.partner =$('#selPartner');
        
        $(document).on('change', '#selDivision', function (e) {
            var id = this.value;
            var partner =  $("#selPartner").val();
            that.district.trigger('change');

            if (!id) {
                return false;
            }
            $.get('/location/districts-as-option-list/' + id, function (resp) {
                that.district.html(resp);
            });
            if (!partner){
                $.get('/outreach/supervisor/supervisor-option/' +'div/' + id, function (resp) {
                    that.supervisor.html(resp);
                });  
            }else{
                $.get('/outreach/supervisor/supervisor-option/' +'div/' + id+'/'+partner, function (resp) {
                    that.supervisor.html(resp);
                });  
            }
            
        });

        $(document).on('change', '#selDistrict', function (e) {
            var partner =  $("#selPartner").val();
            var id = this.value;
            that.upazila.trigger('change');

            if (!id) {
                return false;
            }
            $.get('/location/upazillas-as-option-list/' + id, function (resp) {
                that.upazila.html(resp);
            });
            if (!partner){
                $.get('/outreach/supervisor/supervisor-option/' +'dist/' + id, function (resp) {
                    that.supervisor.html(resp);
                });  
            }else{
                $.get('/outreach/supervisor/supervisor-option/' +'dist/' + id+'/'+partner, function (resp) {
                    that.supervisor.html(resp);
                });  
            }

        });

        $(document).on('change', '#selUpazila', function (e) {
            var partner =  $("#selPartner").val();
            var id = this.value;
            if (!id) {
                return false;
            }
            $.get('/location/unions-as-option-list/' + id, function (resp) {
                that.union.html(resp);
            });
            
            if (!partner){
                $.get('/outreach/supervisor/supervisor-option/' +'upz/' + id, function (resp) {
                    that.supervisor.html(resp);
                });  
            }else{
                $.get('/outreach/supervisor/supervisor-option/' +'upz/' + id+'/'+partner, function (resp) {
                    that.supervisor.html(resp);
                });  
            }

        });
        
        $(document).on('change', '#selPartner', function (e) { 
            //alert('***');
            var divId =  $("#selDivision").val();
            var distId =  $("#selDistrict").val();
            var upzId =  $("#selUpazila").val();
            var location_type = 'partner';
            var id = '-1';
            var partner = this.value;
            
            if(upzId != ''){
                location_type = 'upz';
                id = upzId;
            } else if(distId != ''){
                location_type = 'dist';
                id = distId;
            } else if(divId != ''){
                location_type = 'div';
                id = divId;
            }
            
            $.get('/outreach/supervisor/supervisor-option/' + location_type + '/' +id+'/'+partner, function (resp) {
                that.supervisor.html(resp);
            });
        });
    }
}