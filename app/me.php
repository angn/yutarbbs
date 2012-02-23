<?php

if ($_POST) {
    if (isset($_POST['phone'])) {
        update('users', array('updated_on' => now()) + $_POST, array('uid = ?', $_POST['uid']), 1);
        redirect(null);

    } elseif (isset($_POST['passwd'])) {
        $passwd = $_POST['passwd'];
        if ($passwd && $passwd == $_POST['passwd2']) {
            $passwd = hashpasswd($passwd);
            alert(update('users', compact('passwd'), array('uid = ?', $_POST['uid']), 1) ?
                  '변경되었습니다.' : '변경 실패!');
        } else {
            alert('암호 이상해!');
        }
        redirect(null);
    }

    notfound();
}

$user = fetchone('uid, userid, year, name, email, phone, remark, UNIX_TIMESTAMP(updated_on) updated FROM users WHERE uid = ?', $my->uid) or notfound();
?>
<h2>정보수정</h2>

<form method=post class=me>
<h3><?= h($user->userid) ?></h3>
<table>
<tr><th>이름<td><?= h($user->name) ?>
<tr><th>학번<td><?= h($user->year) ?>
<tr><th>이메일<td><input type=text name=email size=40 value="<?= h($user->email) ?>">
<tr><th>전화번호<td><input type=text name=phone size=40 value="<?= h($user->phone) ?>">
<tr><th>남김말<td><textarea name=remark cols=80 rows=8><?= h($user->remark) ?></textarea>
<tr><th>최종 수정<td><?= formatdate($user->updated) ?>
<tr><th><td><input type=submit value="저장&raquo;" accesskey=s>
</table>
<input type=hidden name=uid value="<?= h($user->uid) ?>">
</form>

<form method=post class=me>
<h3>암호 변경</h3>
<table>
<tr><th>암호<td><input type=password name=passwd>
<tr><th>한 번 더<td><input type=password name=passwd2>
<tr><th><td><input type=submit value="변경&raquo;">
</table>
<input type=hidden name=uid value="<?= $user->uid ?>">
</form>
