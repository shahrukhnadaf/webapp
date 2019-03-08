<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $('.editBtn').on('click',function(){
        //hide edit span
        $(this).closest("tr").find(".editSpan").hide();
        
        //show edit input
        $(this).closest("tr").find(".editInput").show();
        
        //hide edit button
        $(this).closest("tr").find(".editBtn").hide();
        
        //show edit button
        $(this).closest("tr").find(".saveBtn").show();
        
    });
    $('.download').on('click', function(){
        $.ajax({
            type : 'POST',
            url : 'userAction.php',
            dataType : "json",
            data : 'action=download',
            success:function(response){
                if(response.status == 'ok'){
                    //alert(response.msg);
                    $('.down').show();
                }

            }
        })});
    $('.add').on('click',function(){
        //var trObj = $(this).closest("tr");
        //var ID = $(this).closest("tr").attr('id');
        var inputData = $(this).closest("tr").find(".edit").serialize();
        //alert(inputData);
        $.ajax({
            type:'POST',
            url:'userAction.php',
            dataType: "json",
            data:'action=add&'+inputData,
            success:function(response){
                if(response.status == 'ok'){
                    //alert(response.msg);
                    var x = response.msg;
                    //alert(x);
                    detail = JSON.parse(x);
                    //{"MISID":"111603045","FullName":"Abhishek Palaskar","YearOfAdmission":"2016","Fees":"43000","CGPA":"8"}
                    var a = detail['MISID'];
                    var b = detail['FullName'];
                    var c = detail['YearOfAdmission'];
                    var d = detail['Fees'];
                    var e = detail['CGPA'];
                    //            var markup = "<tr><td><input type='checkbox' name='record'></td><td>" + name + "</td><td>" + email + "</td></tr>";

                    //$("table tbody").append(markup);
                    var markup = "<tr><td>"+a+"</td><td>"+b+"</td><td>"+c+"</td><td>"+d+"</td><td>"+e+"</td></tr>";//<button>"+EDIT+"</button><button>"+UPDATE+"</button></td></tr>";
                    $('.sd').append(markup);

                }
                else{
                    alert(response.msg);

                }
            }
        });
        //alert(data);
        //$("trObj").append("something");
    });
    
    $('.saveBtn').on('click',function(){
        var trObj = $(this).closest("tr");
        var ID = $(this).closest("tr").attr('id');
        var inputData = $(this).closest("tr").find(".editInput").serialize();
        $.ajax({
            type:'POST',
            url:'userAction.php',
            dataType: "json",
            data:'action=edit&MISID='+ID+'&'+inputData,
            success:function(response){
                if(response.status == 'ok'){
                    alert(response.msg);
                    trObj.find(".editSpan.FullName").text(response.data.FullName);
                    trObj.find(".editSpan.YearOfAdmission").text(response.data.YearOfAdmission);
                    trObj.find(".editSpan.Fees").text(response.data.Fees);
                    trObj.find(".editSpan.CGPA").text(response.data.CGPA);
                    
                    trObj.find(".editInput.FullName").text(response.data.FullName);
                    trObj.find(".editInput.YearOfAdmission").text(response.data.YearOfAdmission);
                    trObj.find(".editInput.Fees").text(response.data.Fees);
                    trObj.find(".editInput.CGPA").text(response.data.CGPA);
                    
                    trObj.find(".editInput").hide();
                    trObj.find(".saveBtn").hide();
                    trObj.find(".editSpan").show();
                    trObj.find(".editBtn").show();
                    
                    
                }else{
                    alert(response.msg);

                }
            }
        });
        alert(data);
        $("trObj").append("something");
    });
    
    $('.deleteBtn').on('click',function(){
        //hide delete button
        $(this).closest("tr").find(".deleteBtn").hide();
        
        //show confirm button
        $(this).closest("tr").find(".confirmBtn").show();
        
    });
    
    $('.confirmBtn').on('click',function(){
        var trObj = $(this).closest("tr");
        var ID = $(this).closest("tr").attr('id');
        $.ajax({
            type:'POST',
            url:'userAction.php',
            dataType: "json",
            data:'action=delete&MISID='+ID,
            success:function(response){
                if(response.status == 'ok'){
                    trObj.remove();
                }else{
                    trObj.find(".confirmBtn").hide();
                    trObj.find(".deleteBtn").show();
                    alert(response.msg);
                }
            }
        });
    });
});
</script>
<?php
//load and initialize database class
require_once 'DB.class.php';
$db = new DB();

//get studentdata from database
$studentdata = $db->getRows('studentdata',array('order_by'=>'MISID DESC'));

//get status message from session
if(!empty($sessData['status']['msg'])){
    $statusMsg = $sessData['status']['msg'];
    $statusMsgType = $sessData['status']['type'];
    unset($_SESSION['sessData']['status']);
}
?>
<div class="container">
    <div class="row">
        <div class="panel panel-default studentdata-content">
            <table class="table table-striped sd">studentData
                <thead>
                    <tr>
                        <th>MISID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Fees</th>
                        <th>CGPA</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="studentData">
                    <?php if(!empty($studentdata)): foreach($studentdata as $student): ?>
                    <tr id="<?php echo $student['MISID']; ?>">
                        <td><?php echo $student['MISID']; ?></td>
                        <td>
                            <span class="editSpan FullName"><?php echo $student['FullName']; ?></span>
                            <input class="editInput fname form-control input-sm" type="text" name="FullName" value="<?php echo $student['FullName']; ?>" style="display: none;">
                        </td>
                        <td>
                            <span class="editSpan YearOfAdmission"><?php echo $student['YearOfAdmission']; ?></span>
                            <input class="editInput lname form-control input-sm" type="text" name="YearOfAdmission" value="<?php echo $student['YearOfAdmission']; ?>" style="display: none;">
                        </td>
                        <td>
                            <span class="editSpan Fees"><?php echo $student['Fees']; ?></span>
                            <input class="editInput Fees form-control input-sm" type="text" name="Fees" value="<?php echo $student['Fees']; ?>" style="display: none;">
                        </td>
                        <td>
                            <span class="editSpan CGPA"><?php echo $student['CGPA']; ?></span>
                            <input class="editInput CGPA form-control input-sm" type="text" name="CGPA" value="<?php echo $student['CGPA']; ?>" style="display: none;">
                        </td>
                        
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-sm btn-default editBtn" style="float: none;">EDIT</button>
                                <button type="button" class="btn btn-sm btn-default deleteBtn" style="float: none;">DELETE</button>
                            </div>
                            <button type="button" class="btn btn-sm btn-success saveBtn" style="float: none; display: none;">Save</button>
                            <button type="button" class="btn btn-sm btn-danger confirmBtn" style="float: none; display: none;">Confirm</button>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="5">No student(s) found......</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<table>
    <tr id = "addRow">
        <td> 
            MISID: <input class="edit mis" type="text" name="mis" value=''>
        </td>
        <td> 
            Full Name: <input class="edit fullname" type="text" name="fullname" value=''>
        </td>
        <td> 
            Year Of Admission: <input class="edit yoi" type="text" name="yoi" value=''>
        </td>
        <td> 
            Fees: <input class="edit fee" type="text" name="fee" value=''>
        </td>
        <td> 
            CGPA: <input class="edit cg" type="text" name="cg" value=''>
        </td>
        <td>
            <button type="button" class="add" >Add</button>
        </td>
</table>
<button type = "button" class = "download" >Download</button>
<p class = "down" style="display: none"><a href='d.csv' download><button>getFile</button></a></p>