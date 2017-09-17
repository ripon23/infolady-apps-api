$(function () {
//    var bundle_list = new Bloodhound({
//        datumTokenizer: Bloodhound.tokenizers.whitespace,
//        queryTokenizer: Bloodhound.tokenizers.whitespace,        
//        prefetch: '/form-bundle/bundle-list',
//        remote: {
//            url: '/form-bundle/bundle-list/%QUERY',
//            wildcard: '%QUERY'
//          }
//    });
    
//    $('#form_bundle_id').typeahead({
//        hint: true,
//        hightlight: true,
//        minLength: 1
//    }, {
//        name: 'bundle_list',
//        source: bundle_list,
//        display: 'form_bundle_id',
//        templates: {
//            empty: [
//                '<div class="tt-error">',
//                'unable to find any <strong>form_bundle_id</strong> that match the current query',
//                '</div>'
//            ].join('\n'),
//            suggestion: Handlebars.compile('<div><strong>{{form_bundle_id}}</strong> - by {{sent_by}}, {{sent_from}}<br><em>Receiving Date: <u>{{receiving_date}}</u>, Total Forms: <strong>{{total_forms}}</strong></em></div>')
//        }
//    });
    
    var agent_list = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,        
        prefetch: '/form-bundle/supervisor-list',
        remote: {
            url: '/form-bundle/supervisor-list/%QUERY',
            wildcard: '%QUERY'
          }
    });
    
    $('#health_worker_id').typeahead({
        hint: true,
        hightlight: true,
        minLength: 1
    }, {
        name: 'agent_list',
        source: agent_list,
        display: 'supervisor_id',
        templates: {
            empty: [
                '<div class="tt-error">',
                'unable to find any <strong>health_worker_id</strong> that match the current query',
                '</div>'
            ].join('\n'),
//            suggestion: Handlebars.compile('<div><div><strong>{{supervisor_id}}</strong> - <u>{{name}}</u>, <em>{{old_partner_key}}</em></div><div><small>{{division}}, <em>{{district}}</em>, <strong>{{upazila}}</strong></small></div></div>')
                 suggestion: Handlebars.compile('<div><div><strong>{{supervisor_id}}</strong> - <u>{{name}}</u>- <u>{{mobile_no}}</u></div></div>')
        }
    });        
});
//
//var TypeHeadReg={
//    agent_list:null,
//    template:null,
//    init:function(){
//        var that = this;                
//                
//        that.agent_list = new Bloodhound({
//            datumTokenizer: Bloodhound.tokenizers.whitespace,
//            queryTokenizer: Bloodhound.tokenizers.whitespace,        
//            prefetch: '/form-bundle/agent-list',
//            remote: {
//                url: '/form-bundle/agent-list/%QUERY',
//                wildcard: '%QUERY'
//              }
//        });
//        that.template = $('#healthWorkerTemplate').html();
//        
//        that.healthWorkerClass($('.health_worker_id'));
//        $('#btnHealthWorkerForm').click(function(e){
//            e.preventDefault();
//            that.healthWorkerList();                        
//        });             
//    },    
////    healthWorkerList:function(){
////        var that = this;                
////        $('.health_worker_id').typeahead('destroy');
////        var obj = $('#healthWorkerWrapper').append(that.template).find('.health_worker_id');        
////        that.healthWorkerClass(obj);
////    },
////    healthWorkerClass:function(obj){
////        var that = this;
////        $(obj).typeahead({
////            hint: true,
////            hightlight: true,
////            minLength: 1
////        }, {
////            name: 'agent_list',
////            source: that.agent_list,
////            display: 'tx_agent_id',
////            templates: {
////                empty: [
////                    '<div class="tt-error">',
////                    'unable to find any <strong>health_worker_id</strong> that match the current query',
////                    '</div>'
////                ].join('\n'),
////                suggestion: Handlebars.compile('<div><div><strong>{{tx_agent_id}}</strong> - <u>{{tx_agent_name_en}}</u>, <em>{{old_partner_key}}</em></div><div><small>{{division}}, <em>{{district}}</em>, <strong>{{upazila}}</strong></small></div></div>')
////            }
////        });
////    }
//}