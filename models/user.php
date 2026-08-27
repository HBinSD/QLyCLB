<?php 

function getUserInfo($uid){
    $sql = "select 
        us.username,
        us.role,
        u.fullname,
        u.DOB,
        u.phone,
        u.status
    from user as us
    left join userinfo as u
    on us.username = u.username;
    ";
    $statement = db() -> prepare($sql);
    $statement -> execute();
    $result = $statement -> fetchall();
}

?>