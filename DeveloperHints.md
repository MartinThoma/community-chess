# Set up version #
Download the latest code:
```
# Non-members may check out a read-only working copy anonymously over HTTP.
svn checkout http://community-chess.googlecode.com/svn/trunk/ community-chess-read-only
```

  * Download the [latest version of phpBB](http://www.phpbb.com/downloads/olympus.php?from=submenu) and put it in community-chess/forum
  * Install phpBB3
  * Make everything I mentioned in install/phpBB3
  * Download the [german language pack](http://www.phpbb.com/languages/)

# Create a downloadable version #
```
find community-chess/ -path '*/.*' -prune -o -type f -print | zip ~/community-chess.zip -@
```

# Translations #
This will come soon. You might want to take a look at:
  * [Poedit](http://en.wikipedia.org/wiki/Poedit)
  * [GNU gettext](http://en.wikipedia.org/wiki/GNU_gettext)

The translations are located at community-chess/i18n.

# PDO #
You can debug PDO problems with:

```
print_r($conn->errorInfo());
var_dump($stmt);
$stmt->debugDumpParams();
```

It is sometimes helpful to [activate SQL-logging](http://security.stackexchange.com/questions/8865/do-i-have-to-make-any-more-check-if-i-use-prepared-statements-for-integers) and add `$conn->query("SELECT 1");`. This way you can see which statements get executed.