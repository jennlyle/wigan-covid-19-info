<?php

$Title = "News | Wigan COVID-19 Tracker";
$PageDescription = "News related to initiatives around the Wigan based COVID-19 Tracker";
$PageKeywords = "News, Wigan COVID-19 Tracker, Wigan, North West England, COVID-19, coronavirus, lockdown";

include('header.php');
?>
  </head>
  <body>
  <div class="container">
    <header><h1><?php echo $Title; ?></h1></header>

<?php include('navigation.php'); ?>

    <div class="container">
    <p>
        <h3>7th July 2020</h3>
        <p>I’ve been on a ‘high-priority’ … ‘career-advantage’ sort of project so I’ve not been able to update this website like I’d like to.  
        But then also, every way I’ve gotten good data in the past has now changed.  But I’ve had a few hours tonight to spend back on the site, 
        to start re-tooling it for newer ways of how UK PHE and NHS Digital reports their data.  Its my every hope to be able to present as 
        close to auto-magic and on demand, granular local data so that people can make all the right choices for themselves.</p>

        <p>It remains my personal opinion that while UK government hasn’t stated ‘Herd Immunity’ as an official policy on paper, it certainly seems to be that in practice.</p>

    </p>

    <p>
        <h3>26th June 2020</h3>
        Take 2 on BETA…
    </p>

    <hr/>

    <p>
        <h3>15th June 2020</h3>
        I am super excited to push this website out to ‘BETA’ … data here at present is from 06/06/2020, so this is NOT current data.  
        And I’ve got some great friends who are digging into the website and pointing out all the bugs, 
        which I am super happy about because it helps me make this web tool better.  I am so grateful for 
        all the support folks are giving me on this. 
    </p>

    <hr/>

    <p>
        <h3>9th June 2020</h3>
        After taking a few days off to work on other projects, I’m returning here.  I’m going to load new data locally and then see if I can upload everything all together… and have it play nicely. 
    </p>

    <hr/>

    <p>
        <h3>6th June 2020</h3>
        I got so much done with the website today.  I’m about to go into some babble that might not make much sense to anyone else.
        <ul>
            <li>After getting Google Charts to play nicely, I made up some nice tables in Bootstrap.</li>
            <li>I set up the Summary table on the People in Hospital page.</li>
            <li>I got 3 Google charts to play nicely all together on the People in Hospital page.</li>
            <li>I set up the Hospital Deaths Announced page and learned a lot about setting overlays on Google Charts, which I wanted 
                to use for showing where Provisional data sat.</li>
            <li>I set up the Labs Confirmed page, following on from what I learned above.</li>
            <li>I spent a lot of time on the Wigan R page, and I’m still slicing and dicing R at the moment.  Other, more academic 
                groups have access to information such as… “When someone donated blood today, we found coronavirus particles…” that 
                I don’t have access to.  So I’m trying to find a way to calibrate my calculations for R versus what other groups 
                are calculating so that my calculation may somehow be more accurate.</li>
            <li>Also, set up content for the Home page, News page, Sources & Links page, About page, and Disclaimer page.</li>
            <li>Near the end of the day, I restricted Google Charts so that only the last 30 days would show up on most of my charts.</li>
            <li>Refreshed the local database.</li>
            <li>Compared all local website data against the Excel worksheets I’ve been running, and everything looked good.</li>
            <li>I tried to upload everything to push it out to Beta Release, but it didn’t play well.  But I got a lot done today.</li>
            <li>Also discovering the happy joys of what’s different between my development machine and my hosting machine.  Sunshine and rainbows!</li>

        </ul>
    </p>


<hr/>


    <p>
        <h3>5th June 2020</h3>
        The complaint filed at WWL PALS has been passed on to the Information Governance team at WWL.
    </p>
    <p>
        In addition, I was able to get Google Graphs to play nicely and so the real work on this website can finally begin!
    </p>

<hr/>

    <p>
        <h3>4th June 2020</h3>
        I filed a complaint to Wrightington, Wigan and Leigh NHS Trust PALS requesting that they publish their own local data separate 
        from national sources currently on offer.  For justification, I cited some of the incorrect reporting being done by Wigan Today 
        with their misleading headlines.  I further asserted that what with Bryn Ward being built, which could represent a potential 
        reservoir for COVID-19 infection, our community could be further exposed to coronavirus from all over the region and that members 
        of our community could not possibly rely on national figures for their own safety and decision making.
    
    </p>

    </div>

<?php include('footer.php'); ?>