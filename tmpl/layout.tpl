<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Virtual Assistant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="static/bootstrap.min.css" rel="stylesheet">
    <link href="static/style.css" rel="stylesheet">
    <link href="static/bootstrap-responsive.min.css" rel="stylesheet">

    <!--[if lt IE 9]>
      <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="/">Virtual Assistant</a>
          <div class="nav-collapse">
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
      {if $smarty.session.rack.error ne ""}
        <div class="alert alert-error">
          {$smarty.session.rack.error}
        </div>
      {/if}
      {block "content"}{/block}

      <hr>

      <footer>
        <p>&copy; All rights reserved.</p>
      </footer>

    </div> <!-- /container -->
    <script src="static/jquery.js"></script>
    <script src="static/bootstrap-modal.js"></script>
    <script src="static/bootstrap-typeahead.js"></script>
  </body>
</html>