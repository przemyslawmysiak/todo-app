
jQuery('section.todo-container').ready(function () {
      // new task
      jQuery('input.add-new').on('keypress',function(e) {
        task_name = jQuery(this).attr('value');
        task_name = task_name.replace(/"/g, "&quot;").replace(/'/g, "&quot;");
        if(!task_name.trim().length) return;
          if(e.which == 13) {
            jQuery.ajax({
                  type : "POST",
                  dataType : "json",
                  url : "/wp-admin/admin-ajax.php",
                  data : {
                    security:   ajax_nonce.security,
                    action:     "ajax_data",
                    req:        "add",
                    task_name:  task_name,
                  },
                  success: function(response) {

                      jQuery('input.add-new').val('');
                      var new_item  = "<li class=\"tasks\" data-id="+response+"\">";
                          new_item += "<input type=\"checkbox\" />";
                          new_item += "<input type=\"text\" class=\"tasks\" value=\""+task_name+"\"></li>";
                      jQuery('#todo-tasks').append(new_item);
                  }
            });
          }
      });
      // edit task
      jQuery('li.tasks input.tasks').on('keypress',function(e) {
      task_name = jQuery(this).attr('value');
      task_name = task_name.replace(/"/g, "&quot;").replace(/'/g, "&quot;");
      pid       = jQuery(this).parent().data('id');
          if(e.which == 13) {
            jQuery.ajax({
                  type : "POST",
                  dataType : "json",
                  url : "/wp-admin/admin-ajax.php",
                  data : {
                    security:   ajax_nonce.security,
                    action:     "ajax_data",
                    req:        "update",
                    task_name:  task_name,
                    pid:        pid
                  },
            });
          }
      });
      // check / uncheck task
      jQuery('li.tasks').on('change', 'input[type=checkbox]',function(e){
      status = (jQuery(this).is(':checked') == true) ? 1 : 0;
      pid       = jQuery(this).parent().data('id');
        jQuery.ajax({
              type : "POST",
              dataType : "json",
              url : "/wp-admin/admin-ajax.php",
              data : {
                security:   ajax_nonce.security,
                action:     "ajax_data",
                req:        "status",
                pid:        pid,
                status:     status
              },
        });
      });
      // delete ask
      jQuery('li.tasks input.tasks').on('keypress',function(e) {
        pid       = jQuery(this).parent().data('id');
          if(e.which == 13) {
            if(task_name === "") {
              jQuery.ajax({
                    type : "POST",
                    dataType : "json",
                    url : "/wp-admin/admin-ajax.php",
                    data : {
                      security:   ajax_nonce.security,
                      action:     "ajax_data",
                      req:        "delete",
                      pid:        pid
                    },
              });
              jQuery(this).parent().remove();
          }
          }
      });
});
