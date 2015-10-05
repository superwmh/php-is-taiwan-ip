使用方式：
```
require 'is_taiwan_ip.php';

echo is_taiwan_ip($_SERVER['REMOTE_ADDR']);
```

IP-to-Country 的資料庫大約一個月更新一次，如果你發現我沒有即時更新，也可以自己更新。直接從 SVN 下載後，有個 utility 的目錄，透過 PHP 執行 auto-update.php 即可：
```
php auto-update.php
```