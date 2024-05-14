<?php
if(isset($_GET['type']) && isset($_GET['title']) && isset($_GET['content'])) {
    if($_GET['type'] === 'text') {
        $title = urldecode($_GET['title']);
        $content = urldecode($_GET['content']);
        
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $title . '.txt"');
        
        echo $content;
        exit;
    }
}

header("Location: main.php");
exit;
?>
