<!DOCTYPE html>
<html lang="en">
<head>
  <title>Free Subscriber</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link href="datepicker/bootstrap-datetimepicker.min.css" rel="stylesheet">
<script src="js/jquery-2.1.4.min.js"></script>
<script src="js/combodate.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link href="datepicker/bootstrap-datetimepicker.min.css" rel="stylesheet">
  <script src="datepicker/moment-with-locales.js"></script>
  <script src="datepicker/bootstrap-datetimepicker.min.js"></script>
</head>
<body>
<?php 
	include('config.php');
?>
<div class="jumbotron text-center">
  <h1>Search Free Subscriber</h1>
</div>
  
<div class="container">
  <div class="row">
  <form class="form-inline" method="get" action="">
      
      <div class="panel-heading">                    
           <div class="pad">              
              <div class='input-group date' id='datetimepicker1'>
                 <label class="control-label">From</label>
                <input name="fr" type='text' class="form-control" value="<?php echo $from; ?>" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
              </div>
              <div class='input-group date' id='datetimepicker'>
               <label class="control-label">To</label>
                <input name="to" type='text' class="form-control" value="<?php echo $to; ?>" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
              </div>                                                  
              <div class="form-group form-group-sm">                                      
              <button type="submit" class="btn btn-sm btn-primary">Search </button>
              </div>
          </div>
      </div>

    
   </form>
    <div class="panel-heading">
     <table>
        <tr>
         <td>Operations</td>         
         <td><?php echo $operations_total; ?></td>
       </tr>
       <tr>
         <td>Pmrs</td>
         <td><?php echo $pmrs_total; ?></td>
       </tr>
       <tr>
         <td>Total</td>
         <td><?php echo ($operations_total + $pmrs_total); ?></td>
       </tr>
     </table>
   </div>
  </div>   
</div>

</body>
</html>
<script type="text/javascript">
    $(function () {                   
        $('#datetimepicker1').datetimepicker({
             format: 'YYYY-MM-DD'
         }),
        $('#datetimepicker').datetimepicker({
             format: 'YYYY-MM-DD'
         });
    });
</script>  
<style type="text/css">
  table{
    width: 30%;
    height: auto;    
  }
  table tr {
    line-height: 2px;      
  }
  table tr td {
    padding: 12px;
    border:solid 1px gray;      
  }
</style>










