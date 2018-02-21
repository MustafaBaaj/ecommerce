</div><br /> <br />
<div class="col-md-12 text-center">&copy; copyright 2018-2019 mustafa baaj</div>

<script>
function updatesizes(){
    var sizestring ='';
    for(var i=1;i<=12;i++){
        if(jQuery('#size'+i).val() != ''){
            sizestring += jQuery('#size'+i).val()+':'+jQuery('#qty'+i).val()+',';
        }
    }
    jQuery('#sizes').val(sizestring);
}
function get_child_options(){
    var parentID = jQuery('#parent').val();
    jQuery.ajax({
        url: '/ecommerce/admin/parsers/child_categories.php',
        type: 'POST',
        data: {parentID : parentID},
        success: function(data){
            jQuery('#child').html(data);
        },
        error: function(){alert("somthing went wrong in th child options.")},
    });

}
    jQuery('select[name="parent"]').change(get_child_options);
</script>

</body>
</html>
