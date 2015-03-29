# Introduction #
I am quite sure that community chess will always run on a MySQL server as most providers support only MySQL. So is there any reason to use a data-access abstraction layer?

I've also asked this question on [stackoverflow](http://stackoverflow.com/questions/7535074/should-project-that-will-always-use-mysql-use-pdo).

# data-access abstraction layers #
The data-access abstraction layer should either be directly implemented into PHP or have very few files to be added to my project. The licence has to be MIT-compatible.

  * [PDO](http://php.net/manual/en/book.pdo.php)
  * [GDO](http://trac.gwf3.gizmore.org/browser/core/inc/GDO) - Gizmore Data Objects