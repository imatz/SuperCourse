<div style="margin-left: 20px;">
<h2>{__("video_tutorials")}</h2>
</div>

<style>
div.container {
    width: 100%;
    border: 0px;
}

header, footer {
    padding: 1em;
    color: black;
    background-color: white;
    clear: left;
    text-align: center;
}

nav {
    float: left;
    max-width: 40%;
    margin: 0;
    padding: 1em;
}

nav ul {
    list-style-type: none;
    padding: 0;
}
   
nav ul a {
    text-decoration: none;
}

article {
    margin-left: 54%;
	max-width: 44%;
    border-left: 0px;
    padding: 1em;
    overflow: hidden;
}
</style>


{if $auth.account_type=="B"}

<div class="container">
	<nav>
  		<ul>
			<li>
				<h3>{__("bookstores_orders")}</h3>
            	<iframe width="510" height="287" src="https://www.youtube.com/embed/PjyqwUQBqr4?rel=0" frameborder="0" allowfullscreen></iframe>
			</li>
		</ul>
	</nav>

	<article>
	</article>
</div>
{elseif $auth.account_type=="S"}
<div class="container">
	<nav>
		<ul>
			<li>
				<h3>{__("front_orders")}</h3>
          	  <iframe width="510" height="287" src="https://www.youtube.com/embed/sMS4R5ZeHVQ?rel=0" frameborder="0" allowfullscreen></iframe>
			</li>
			<li>
				<h3>{__("personal_product_codes")}</h3>
            	<iframe width="510" height="287" src="https://www.youtube.com/embed/UM6T1JoIzlo?rel=0" frameborder="0" allowfullscreen></iframe>
			</li>	
		</ul>
	</nav>
</div>

{else}
<div class="container">
	<nav>
		<ul>
			<li>
				<h3>{__("register")}</h3>
          	  <iframe width="510" height="287" src="https://www.youtube.com/embed/8wk6cFCUMWg?rel=0" frameborder="0" allowfullscreen></iframe>
			</li>
			<li>
				<h3>{__("retail_customers_orders")}</h3>
            	<iframe width="510" height="287" src="https://www.youtube.com/embed/Tpcbm0QWEqI?rel=0" frameborder="0" allowfullscreen></iframe>
			</li>	
		</ul>
	</nav>

<article>
  <ul>
	<li>
			<h3>{__("login")}</h3>
            <iframe width="510" height="287" src="https://www.youtube.com/embed/Z88u2_j8BRI?rel=0" frameborder="0" allowfullscreen></iframe>
	</li>
</ul>
</article>

</div>
{/if}
