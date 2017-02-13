<?php

/*
 * MailWatch for MailScanner
 * Copyright (C) 2003-2011  Steve Freegard (steve@freegard.name)
 * Copyright (C) 2011  Garrod Alwood (garrod.alwood@lorodoes.com)
 * Copyright (C) 2014-2017  MailWatch Team (https://github.com/mailwatch/1.2.0/graphs/contributors)
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 *
 * In addition, as a special exception, the copyright holder gives permission to link the code of this program with
 * those files in the PEAR library that are licensed under the PHP License (or with modified versions of those files
 * that use the same license as those files), and distribute linked combinations including the two.
 * You must obey the GNU General Public License in all respects for all of the code used other than those files in the
 * PEAR library that are licensed under the PHP License. If you modify this program, you may extend this exception to
 * your version of the program, but you are not obligated to do so.
 * If you do not wish to do so, delete this exception statement from your version.
 *
 * As a special exception, you have permission to link this program with the JpGraph library and distribute executables,
 * as long as you follow the requirements of the GNU GPL in regard to all of the software in the executable aside from
 * JpGraph.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free
 * Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require_once __DIR__ . '/functions.php';
if (file_exists('conf.php')) {
    $output = array();
    if (isset($_GET['mid'], $_GET['r'])) {
        dbconn();
        $mid = safe_value($_GET['mid']);
        $token = safe_value($_GET['r']);
        $sql = "SELECT * FROM autorelease WHERE msg_id = '$mid'";
        $result = dbquery($sql);
        if (!$result) {
            dbg('Error fetching from database' . database::$link->error);
            $output[] = __('dberror59');
        }
        if ($result->num_rows === 0) {
            $output[] = __('msgnotfound159');
            $output[] = __('msgnotfound259') . htmlentities($mid) . ' ' . __('msgnotfound359');
        } else {
            $row = $result->fetch_assoc();
            if ($row['uid'] === $token) {
                $list = quarantine_list_items($mid);
                $result = '';
                if (count($list) === 1) {
                    $to = $list[0]['to'];
                    $result = quarantine_release($list, array(0), $to);
                } else {
                    $listCount = count($list);
                    for ($i = 0; $i < $listCount; $i++) {
                        if (preg_match('/message\/rfc822/', $list[$i]['type'])) {
                            $result = quarantine_release($list, array($i), $list[$i]['to']);
                        }
                    }
                }
                //success
                $output[] = __('msgreleased59');
                //cleanup
                $releaseID = $row['id'];
                $query = "DELETE FROM autorelease WHERE id = '$releaseID'";
                $result = dbquery($query);
                if (!$result) {
                    dbg('ERROR cleaning up database... ' . database::$link->error);
                }
            } else {
                $output[] = __('tokenmismatch59');
            }
        }
    } else {
        $output[] = __('notallowed59');
    } ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo __('title59'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/favicon.png">
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body class="autorelease">
<div class="autorelease">
    <img src="<?php echo MAILWATCH_HOSTURL . IMAGES_DIR . MW_LOGO; ?>" alt="<?php echo __('mwlogo99'); ?>">
    <div class="border-rounded">
        <h1><?php echo __('title63'); ?></h1>
        <?php
        foreach ($output as $msg) {
            echo '<p>' . $msg . '</p>';
        } ?>
    </div>
</div>
</body>
</html>
<?php

} else {
    echo __('cannot_read_conf');
}
