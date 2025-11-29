<!DOCTYPE html>
<html>
<head>
    <title>Draft <?= htmlspecialchars($no_ppa) ?></title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
        }
        iframe {
            border: none;
            width: 100%;
            height: 100%;
            display: block;
        }
    </style>
</head>
<body>
    <iframe src="<?= base_url('uploads/ppa/' . $draft_file) ?>" allowfullscreen></iframe>
</body>
</html>
