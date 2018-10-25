[[$bHeader]]
<div class="container">
    <div class="row">
        [[*NavBox:notempty=`
            <div class="span3 hide-phone">
                [[*NavBox]]
            </div>
        `]]
        <div class="[[*NavBox:isnot=``:then=`span9`:else=`span12`]] content tester">
        [[Breadcrumbs]]
        <div class="page-header">
        <h1>[[*longtitle]]</h1>
        </div>
        [[*content]]
        </div>
    </div>
    <hr>
    <footer>
        <p>[[$Footer]]</p>
    </footer>
</div> 
<!-- /container -->
[[$bFooter]]