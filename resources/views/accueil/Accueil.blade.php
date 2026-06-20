@extends('layouts.app')

@section('title', __('app.home'))

@section('content')
<header>
    <h1 class="text-center">Bienvenue à Notre école !</h1>
</header>
    <div class="container" style="padding: 50px; display: flex;">
        {{-- <h1>Bienvenue à Notre école !</h1> --}}

        <div class="jumbotron">
            <h1></h1>
            <p>Notre école est un lieu d'apprentissage dynamique dédié à l'excellence académique et au développement
                personnel. Nous offrons une éducation de qualité pour les élèves de tous les niveaux, en mettant l'accent
                sur la créativité, la collaboration et la pensée critique.</p>
            <p>Grâce à notre équipe d'enseignants hautement qualifiés et à nos installations modernes, nous créons un
                environnement propice à l'épanouissement de chaque élève. Nous encourageons l'exploration, la curiosité et
                l'innovation, tout en fournissant un soutien individualisé pour répondre aux besoins uniques de chaque
                élève.</p>
            <p>Que vous soyez un étudiant en quête de connaissances, un parent soucieux du bien-être de votre enfant ou un
                membre de la communauté intéressé par notre école, nous vous invitons à rejoindre notre communauté éducative
                dynamique.</p>
            <p>Explorez notre site pour en savoir plus sur nos programmes, nos activités parascolaires et nos réalisations.
                Nous sommes impatients de vous accueillir dans notre école et de vous accompagner dans votre parcours
                d'apprentissage et de réussite.</p>
        </div>
        <div class="svg-container">
            <style>
                footer{
                    background: #d0d0d0;
                    padding: 10px;
                }
                header{
                    background: #d0d0d0;
                    top: 20px;
                    padding: 53px 0 1px 0;
                }
                .jumbotron {
                    width: 70%;
                    display: inline-block;
                    vertical-align: top;
                }

                .svg-container {
                    width: 30%;
                    display: inline-block;
                }

                svg#freepik_stories-end-of-school:not(.animated) .animable {
                    opacity: 0;
                }

                svg#freepik_stories-end-of-school.animated #freepik--Plant--inject-25 {
                    animation: 1s 1 forwards cubic-bezier(.36, -0.01, .5, 1.38) lightSpeedRight;
                    animation-delay: 0s;
                }

                svg#freepik_stories-end-of-school.animated #freepik--Papers--inject-25 {
                    animation: 3s Infinite linear floating;
                    animation-delay: 0s;
                }

                @keyframes lightSpeedRight {
                    from {
                        transform: translate3d(50%, 0, 0) skewX(-20deg);
                        opacity: 0;
                    }

                    60% {
                        transform: skewX(10deg);
                        opacity: 1;
                    }

                    80% {
                        transform: skewX(-2deg);
                    }

                    to {
                        opacity: 1;
                        transform: translate3d(0, 0, 0);
                    }
                }

                @keyframes floating {
                    0% {
                        opacity: 1;
                        transform: translateY(0px);
                    }

                    50% {
                        transform: translateY(-10px);
                    }

                    100% {
                        opacity: 1;
                        transform: translateY(0px);
                    }
                }
            </style>
            <svg class="animated" id="freepik_stories-end-of-school" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 750 500"
                version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs">
                <g id="freepik--background-simple--inject-25" class="animable"
                    style="transform-origin: 361.776px 250.546px;">
                    <g id="elnexrgvxhhys" class="animable" style="transform-origin: 361.776px 250.546px;">
                        <path
                            d="M263.6061,119.8167c97.474-47.9938,197.7297-110.75173,310.9669-85.0694,35.11029,7.963,67.06209,27.8697,88.66079,56.811,57.7534,77.3871,53.0569,195.9204,21.6791,283.0329-4.09489,11.3685-9.2083,22.3925-15.7873,32.5468-31.8473,49.1544-85.5131,51.4702-137.2762,37.2592-43.805-12.0263-82.6145-24.3986-127.8645-12.1703-25.0633,6.7731-49.35,16.0819-74.3206,23.1665-33.9971,9.6457-69.0461,16.1986-104.4522,16.8625-93.66783,1.7565-200.6653-46.0274-208.9647-150.6306-.5378-6.7791-.6327-13.5921-.3283-20.3851,1.8712-41.7591,15.6719-83.5788,53.9252-105.2597,38.4593-21.7975,84.089-30.416,125.2278-45.876,23.3938-8.7915,46.0923-19.2381,68.534-30.2878Z"
                            style="fill: #92E3A9; transform-origin: 361.776px 250.546px;" id="elojydmbrp3ht"
                            class="animable"></path>
                        <g id="elxteyiw98zbg">
                            <path
                                d="M263.6061,119.8167c97.474-47.9938,197.7297-110.75173,310.9669-85.0694,35.11029,7.963,67.06209,27.8697,88.66079,56.811,57.7534,77.3871,53.0569,195.9204,21.6791,283.0329-4.09489,11.3685-9.2083,22.3925-15.7873,32.5468-31.8473,49.1544-85.5131,51.4702-137.2762,37.2592-43.805-12.0263-82.6145-24.3986-127.8645-12.1703-25.0633,6.7731-49.35,16.0819-74.3206,23.1665-33.9971,9.6457-69.0461,16.1986-104.4522,16.8625-93.66783,1.7565-200.6653-46.0274-208.9647-150.6306-.5378-6.7791-.6327-13.5921-.3283-20.3851,1.8712-41.7591,15.6719-83.5788,53.9252-105.2597,38.4593-21.7975,84.089-30.416,125.2278-45.876,23.3938-8.7915,46.0923-19.2381,68.534-30.2878Z"
                                style="fill: rgb(255, 255, 255); opacity: 0.7; transform-origin: 361.776px 250.546px;"
                                class="animable" id="el0pcz67so4zh"></path>
                        </g>
                    </g>
                </g>
                <g id="freepik--Plant--inject-25" class="animable" style="transform-origin: 692.511px 316.111px;">
                    <g id="elaez1xc6xtgj" class="animable" style="transform-origin: 692.511px 316.111px;">
                        <g id="el2lin88xgaii" class="animable" style="transform-origin: 677.483px 310.892px;">
                            <line x1="671.71359" y1="245.1604" x2="686.94" y2="391.0593"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 679.327px 318.11px;"
                                id="elr31ldaposm" class="animable"></line>
                            <g id="elk85rufsastf" class="animable" style="transform-origin: 677.483px 305.228px;">
                                <g id="elueanvr7qgyd" class="animable" style="transform-origin: 674.685px 355.91px;">
                                    <line x1="684.486" y1="363.1379" x2="677.58339" y2="355.8472"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 681.035px 359.493px;"
                                        id="elm6fo1xbav6q" class="animable"></line>
                                    <path
                                        d="M679.68039,358.0317s.19761-3.2252-2.7473-5.8553c-1.7718-1.5823-11.0964-5.505-11.9813-2.2208-.9624,3.572,8.582,10.5868,14.7286,8.0761Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 672.284px 353.623px;"
                                        id="elbmvw45ni8gn" class="animable"></path>
                                    <line x1="679.68039" y1="358.0317" x2="670.80339" y2="352.4999"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 675.242px 355.266px;"
                                        id="elq66v49da5y7" class="animable"></line>
                                </g>
                                <g id="el7boksz7vkxc" class="animable" style="transform-origin: 676.417px 372.503px;">
                                    <line x1="686.21769" y1="379.7307" x2="679.315" y2="372.4401"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 682.766px 376.085px;"
                                        id="el0b0rtik4lwe" class="animable"></line>
                                    <path
                                        d="M681.41209,374.6244s.1976-3.2249-2.7474-5.8549c-1.77169-1.5824-11.0963-5.5052-11.9814-2.221-.9622,3.5717,8.582,10.5868,14.7288,8.0759Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 674.015px 370.215px;"
                                        id="el5dico5xg1it" class="animable"></path>
                                    <line x1="681.41209" y1="374.6244" x2="672.53489" y2="369.0926"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 676.974px 371.858px;"
                                        id="elk2jnp6h4h0c" class="animable"></line>
                                </g>
                                <g id="el7za3u7hhv0b" class="animable" style="transform-origin: 670.459px 315.42px;">
                                    <line x1="680.26029" y1="322.6482" x2="673.35769" y2="315.3576"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 676.809px 319.003px;"
                                        id="eludyf5hy7a8r" class="animable"></line>
                                    <path
                                        d="M675.45479,317.542s.1975-3.225-2.7472-5.8552c-1.7719-1.5822-11.0965-5.5052-11.9815-2.221-.9623,3.5719,8.5818,10.5869,14.7287,8.0762Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 668.058px 313.133px;"
                                        id="el569umutlpu" class="animable"></path>
                                    <line x1="675.45479" y1="317.542" x2="666.57769" y2="312.01"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 671.016px 314.776px;"
                                        id="elqyk7y7izw6" class="animable"></line>
                                </g>
                                <g id="elpi7ttjfj84q" class="animable" style="transform-origin: 672.191px 332.013px;">
                                    <line x1="681.99219" y1="339.241" x2="675.08939" y2="331.9505"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 678.541px 335.596px;"
                                        id="elqpphp61k47" class="animable"></line>
                                    <path
                                        d="M677.18649,334.1348s.19751-3.225-2.7473-5.8552c-1.7717-1.5824-11.0965-5.5051-11.9813-2.221-.9623,3.572,8.5819,10.5869,14.7286,8.0762Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 669.79px 329.726px;"
                                        id="elkc6gyt1vjq" class="animable"></path>
                                    <line x1="677.18649" y1="334.1348" x2="668.30949" y2="328.6029"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 672.748px 331.369px;"
                                        id="eljak5sbcp8eq" class="animable"></line>
                                </g>
                                <g id="elsv2xmd2lyjd" class="animable" style="transform-origin: 667.647px 288.466px;">
                                    <line x1="677.44739" y1="295.6942" x2="670.54459" y2="288.4034"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 673.996px 292.049px;"
                                        id="elq94bb2k999n" class="animable"></line>
                                    <path
                                        d="M672.64179,290.5879s.1975-3.225-2.7471-5.8551c-1.7719-1.5823-11.0965-5.5052-11.9815-2.2208-.9623,3.5717,8.5819,10.5866,14.7286,8.0759Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 665.245px 286.179px;"
                                        id="ele2d9zm3q9kh" class="animable"></path>
                                    <line x1="672.64179" y1="290.5879" x2="663.76459" y2="285.0562"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 668.203px 287.822px;"
                                        id="elwtud6eebkvp" class="animable"></line>
                                </g>
                                <g id="elt9gaj8pmwjj" class="animable" style="transform-origin: 664.821px 261.395px;">
                                    <line x1="674.622" y1="268.6225" x2="667.71939" y2="261.3317"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 671.171px 264.977px;"
                                        id="el1i9e5l5spbkh" class="animable"></line>
                                    <path
                                        d="M669.81669,263.5162s.1975-3.2249-2.7474-5.8549c-1.7718-1.5824-11.09629-5.5054-11.98129-2.2212-.96241,3.5719,8.58189,10.587,14.72869,8.0761Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 662.42px 259.107px;"
                                        id="el2wlj96maudu" class="animable"></path>
                                    <line x1="669.81669" y1="263.5162" x2="660.93949" y2="257.9843"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 665.378px 260.75px;"
                                        id="elpo7yw4fmcg" class="animable"></line>
                                </g>
                                <g id="el1fr5zddczzu" class="animable" style="transform-origin: 691.636px 344.422px;">
                                    <line x1="683.32569" y1="353.3204" x2="688.57439" y2="344.7616"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 685.95px 349.041px;"
                                        id="elzjuk8pjbzjq" class="animable"></line>
                                    <path
                                        d="M686.97319,347.3318s-.859-3.1147,1.4794-6.296c1.4069-1.9142,9.7206-7.6778,11.2646-4.6472,1.6791,3.2963-6.2111,12.1307-12.744,10.9432Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 693.383px 341.483px;"
                                        id="elgenqooi0dni" class="animable"></path>
                                    <line x1="686.97319" y1="347.3318" x2="694.96669" y2="339.6919"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 690.97px 343.512px;"
                                        id="elg7ikxs2kcxs" class="animable"></line>
                                </g>
                                <g id="eln1oahjkev4j" class="animable" style="transform-origin: 687.079px 300.758px;">
                                    <line x1="678.76869" y1="309.6558" x2="684.01739" y2="301.0971"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 681.393px 305.376px;"
                                        id="elxhdez5hgsoo" class="animable"></line>
                                    <path
                                        d="M682.41639,303.6674s-.859-3.1148,1.4792-6.2961c1.407-1.9142,9.7208-7.6778,11.2646-4.647,1.6791,3.2961-6.2109,12.1305-12.7438,10.9431Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 688.826px 297.818px;"
                                        id="el0qc5cs6p7mqc" class="animable"></path>
                                    <line x1="682.41639" y1="303.6674" x2="690.40989" y2="296.0274"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 686.413px 299.847px;"
                                        id="elxol7voimvg" class="animable"></line>
                                </g>
                                <g id="eli9j90raj6md" class="animable" style="transform-origin: 685.077px 281.569px;">
                                    <line x1="676.76639" y1="290.467" x2="682.01489" y2="281.9083"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 679.391px 286.188px;"
                                        id="elhesymutodv9" class="animable"></line>
                                    <path
                                        d="M680.41379,284.4786s-.8592-3.1148,1.47921-6.2963c1.40689-1.914,9.72079-7.6776,11.26459-4.6469,1.6793,3.2962-6.211,12.1306-12.7438,10.9432Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 686.823px 278.629px;"
                                        id="el4puf8lu1fy" class="animable"></path>
                                    <line x1="680.41379" y1="284.4786" x2="688.40739" y2="276.8384"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 684.411px 280.659px;"
                                        id="elsx06zhpb43" class="animable"></line>
                                </g>
                                <g id="elf6iit0qnzkl" class="animable" style="transform-origin: 675.95px 246.53px;">
                                    <line x1="673.83049" y1="262.3359" x2="679.07919" y2="253.7772"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 676.455px 258.057px;"
                                        id="eljw0uwrzvyy" class="animable"></line>
                                    <path
                                        d="M677.478,256.3475s-.859-3.1148,1.47939-6.2962c1.4067-1.9139,9.72061-7.6776,11.2644-4.6468,1.6791,3.2961-6.2109,12.1306-12.74379,10.943Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 683.887px 250.498px;"
                                        id="elbkyo87lwpsk" class="animable"></path>
                                    <line x1="677.478" y1="256.3475" x2="685.47149" y2="248.7073"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 681.475px 252.527px;"
                                        id="elis4nsdxndjg" class="animable"></line>
                                    <path
                                        d="M671.58559,244.3501s-.859-3.1146,1.4792-6.2961c1.4069-1.9142,9.7208-7.6778,11.2646-4.6469,1.6791,3.296-6.211,12.1305-12.7438,10.943Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 677.995px 238.501px;"
                                        id="eli8j9cd1tyoc" class="animable"></path>
                                    <line x1="671.58559" y1="244.3501" x2="679.579" y2="236.7101"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 675.582px 240.53px;"
                                        id="elojthfpz26dp" class="animable"></line>
                                    <path
                                        d="M672.04879,244.6797s-3.1877,.5277-6.1064-2.1314c-1.7561-1.5998-6.6159-10.472-3.4401-11.6897,3.4541-1.3242,11.4125,7.449,9.5465,13.8211Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 666.889px 237.723px;"
                                        id="elx7iho2kyh4e" class="animable"></path>
                                    <line x1="672.04879" y1="244.6797" x2="665.28889" y2="235.9291"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 668.669px 240.304px;"
                                        id="el7k2fzd5l5w7" class="animable"></line>
                                </g>
                            </g>
                        </g>
                        <g id="el6tbbkdeks94" class="animable" style="transform-origin: 706.727px 330.249px;">
                            <line x1="711.87339" y1="265.253" x2="696.733" y2="411.1609"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 704.303px 338.207px;"
                                id="elbygv3a0rgl4" class="animable"></line>
                            <g id="elwxk5a0imkp" class="animable" style="transform-origin: 706.727px 316.335px;">
                                <g id="eleed2h4itdss" class="animable" style="transform-origin: 691.766px 374.439px;">
                                    <line x1="700.08049" y1="383.3325" x2="694.827" y2="374.7769"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 697.454px 379.055px;"
                                        id="el09kzbgbp6u9s" class="animable"></line>
                                    <path
                                        d="M696.42949,377.3464s.8572-3.1153-1.4828-6.2954c-1.4083-1.9133-9.7254-7.6719-11.2674-4.6404-1.67729,3.2971,6.2182,12.127,12.7502,10.9358Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 690.017px 371.501px;"
                                        id="elmyejxk8vuc" class="animable"></path>
                                    <line x1="696.42949" y1="377.3464" x2="688.88149" y2="370.1051"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 692.655px 373.726px;"
                                        id="elayjdhsye84" class="animable"></line>
                                </g>
                                <g id="elspwgwss0ihq" class="animable" style="transform-origin: 695.968px 333.947px;">
                                    <line x1="704.28239" y1="342.8402" x2="699.02859" y2="334.2846"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 701.655px 338.562px;"
                                        id="elsyrhhvicnvl" class="animable"></line>
                                    <path
                                        d="M700.63119,336.854s.8573-3.1152-1.4829-6.2953c-1.408-1.9133-9.7252-7.672-11.26729-4.6404-1.67721,3.2972,6.21809,12.1271,12.75019,10.9357Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 694.219px 331.008px;"
                                        id="elac8c5oq9ua6" class="animable"></path>
                                    <line x1="700.63119" y1="336.854" x2="693.08309" y2="329.6127"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 696.857px 333.233px;"
                                        id="el57yqoh4pur3" class="animable"></line>
                                </g>
                                <g id="elo7ajcdn9gv" class="animable" style="transform-origin: 694.246px 350.541px;">
                                    <line x1="702.56039" y1="359.4342" x2="697.30669" y2="350.8785"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 699.934px 355.156px;"
                                        id="elpw82p3gg8c" class="animable"></line>
                                    <path
                                        d="M698.90939,353.4478s.8571-3.1152-1.483-6.2953c-1.4081-1.9131-9.7252-7.6719-11.2673-4.6403-1.6773,3.2973,6.2182,12.127,12.7503,10.9356Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 692.497px 347.602px;"
                                        id="elmjvi2xybhnm" class="animable"></path>
                                    <line x1="698.90939" y1="353.4478" x2="691.36139" y2="346.2067"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 695.135px 349.827px;"
                                        id="el6ce2oqpk5tl" class="animable"></line>
                                </g>
                                <g id="elo7ct40lo86j" class="animable" style="transform-origin: 698.764px 306.991px;">
                                    <line x1="707.07919" y1="315.8846" x2="701.82579" y2="307.329"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 704.452px 311.607px;"
                                        id="elzyjxs9hzkel" class="animable"></line>
                                    <path
                                        d="M703.42829,309.8983s.8572-3.115-1.4829-6.2953c-1.408-1.9132-9.7253-7.6718-11.26739-4.6404-1.67711,3.2972,6.218,12.1269,12.75029,10.9357Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 697.016px 304.053px;"
                                        id="eldad2nbkdx1m" class="animable"></path>
                                    <line x1="703.42829" y1="309.8983" x2="695.88019" y2="302.657"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 699.654px 306.278px;"
                                        id="eljpft1jvetql" class="animable"></line>
                                </g>
                                <g id="elwwb1kxlb2l" class="animable" style="transform-origin: 701.574px 279.918px;">
                                    <line x1="709.88849" y1="288.8114" x2="704.63509" y2="280.2556"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 707.262px 284.534px;"
                                        id="el5vm86pt054w" class="animable"></line>
                                    <path
                                        d="M706.23759,282.8251s.8571-3.1154-1.483-6.2952c-1.408-1.9134-9.7251-7.6722-11.2673-4.6405-1.67729,3.2972,6.218,12.1271,12.7503,10.9357Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 699.825px 276.979px;"
                                        id="elbbdv76904mq" class="animable"></path>
                                    <line x1="706.23759" y1="282.8251" x2="698.68949" y2="275.5838"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 702.464px 279.204px;"
                                        id="el06cbd4y58yzx" class="animable"></line>
                                </g>
                                <g id="el6uzn3x4ijzt" class="animable" style="transform-origin: 710.764px 366.254px;">
                                    <line x1="700.96649" y1="373.4864" x2="707.865" y2="366.1916"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 704.416px 369.839px;"
                                        id="elc211wsv6a5" class="animable"></line>
                                    <path
                                        d="M705.76919,368.3776s-.1995-3.2251,2.7439-5.857c1.7708-1.5834,11.093-5.5116,11.9798-2.2281,.9645,3.5713-8.5755,10.592-14.7237,8.0851Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 713.163px 363.964px;"
                                        id="el81u7c5xxkf" class="animable"></path>
                                    <line x1="705.76919" y1="368.3776" x2="715.16419" y2="362.5468"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 710.467px 365.462px;"
                                        id="elt2gdykankw" class="animable"></line>
                                </g>
                                <g id="el91byagkswb" class="animable" style="transform-origin: 715.295px 322.586px;">
                                    <line x1="705.49779" y1="329.8193" x2="712.396" y2="322.5244"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 708.947px 326.172px;"
                                        id="eljojq3wnw25p" class="animable"></line>
                                    <path
                                        d="M710.30009,324.7101s-.1994-3.2249,2.744-5.8566c1.77091-1.5836,11.0932-5.5117,11.9798-2.2281,.9648,3.5712-8.5755,10.592-14.7238,8.0847Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 717.695px 320.297px;"
                                        id="elkdo23fajjs" class="animable"></path>
                                    <line x1="710.30009" y1="324.7101" x2="719.69558" y2="318.8796"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 714.998px 321.795px;"
                                        id="elneerq13m7k8" class="animable"></line>
                                </g>
                                <g id="elwj9zuqh3jy9" class="animable" style="transform-origin: 717.286px 303.396px;">
                                    <line x1="707.489" y1="310.6292" x2="714.38739" y2="303.3347"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 710.938px 306.982px;"
                                        id="elqyqwyvc6ize" class="animable"></line>
                                    <path
                                        d="M712.29139,305.5202s-.1992-3.225,2.7441-5.8566c1.7707-1.5834,11.093-5.5119,11.9799-2.2282,.9645,3.5713-8.5757,10.592-14.724,8.0848Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 719.686px 301.107px;"
                                        id="elbwzlh31ojiq" class="animable"></path>
                                    <line x1="712.29139" y1="305.5202" x2="721.68678" y2="299.6897"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 716.989px 302.605px;"
                                        id="eltinskfdu2v" class="animable"></line>
                                </g>
                                <g id="el1ge5p2objda" class="animable" style="transform-origin: 717.127px 265.917px;">
                                    <line x1="710.40809" y1="282.4966" x2="717.30649" y2="275.2018"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 713.857px 278.849px;"
                                        id="elyku2uijhcyb" class="animable"></line>
                                    <path
                                        d="M715.21069,277.3876s-.1995-3.2249,2.7438-5.8568c1.7711-1.5834,11.0934-5.5117,11.9801-2.2282,.96442,3.5714-8.5757,10.592-14.7239,8.085Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 722.605px 272.974px;"
                                        id="eljbhpww0337" class="animable"></path>
                                    <line x1="715.21069" y1="277.3876" x2="724.60588" y2="271.5568"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 719.908px 274.472px;"
                                        id="el738xd4v5s8d" class="animable"></line>
                                    <path
                                        d="M711.91479,264.4339s-.1994-3.225,2.7439-5.8569c1.7706-1.5832,11.0931-5.5116,11.98-2.228,.9644,3.5713-8.5756,10.592-14.7239,8.0849Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 719.309px 260.02px;"
                                        id="elugtr51sotm" class="animable"></path>
                                    <line x1="711.91479" y1="264.4339" x2="721.31" y2="258.6032"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 716.612px 261.519px;"
                                        id="el4bfylun3ive" class="animable"></line>
                                    <path
                                        d="M712.30019,264.8518s-3.2281-.1399-5.5366-3.343c-1.389-1.9272-4.3178-11.61-.9595-12.1476,3.6526-.5846,9.6341,9.639,6.4961,15.4906Z"
                                        style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 708.716px 257.095px;"
                                        id="elw5btxdjtw4i" class="animable"></path>
                                    <line x1="712.30019" y1="264.8518" x2="707.48689" y2="254.8968"
                                        style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 709.894px 259.874px;"
                                        id="elhrpes2gqsvo" class="animable"></line>
                                </g>
                            </g>
                        </g>
                        <line x1="694.52329" y1="205.8389" x2="694.52329" y2="390.3489"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 694.523px 298.094px;"
                            id="el4a7x4qc6eft" class="animable"></line>
                        <g id="el03069n41nnwz" class="animable" style="transform-origin: 694.603px 283.606px;">
                            <g id="eljj2km58gf6n" class="animable" style="transform-origin: 685.572px 354.512px;">
                                <line x1="694.67" y1="362.6061" x2="688.56129" y2="354.6386"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 691.616px 358.622px;"
                                    id="elt586qmi8k5l" class="animable"></line>
                                <path
                                    d="M690.42059,357.0287s.5311-3.1871-2.1247-6.1086c-1.5981-1.7576-10.4653-6.6273-11.6861-3.4526-1.3279,3.4526,7.4365,11.4204,13.8108,9.5612Z"
                                    style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 683.469px 351.862px;"
                                    id="elkoa6pgjyaab" class="animable"></path>
                                <line x1="690.42059" y1="357.0287" x2="682.16559" y2="350.6053"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 686.293px 353.817px;"
                                    id="elm5opjvqj96" class="animable"></line>
                            </g>
                            <g id="eluhkzxp7mwa" class="animable" style="transform-origin: 685.572px 319.837px;">
                                <line x1="694.67" y1="327.9319" x2="688.56129" y2="319.9642"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 691.616px 323.948px;"
                                    id="elcalvsbk0nq" class="animable"></line>
                                <path
                                    d="M690.42059,322.3545s.5311-3.1871-2.1247-6.1086c-1.5981-1.7577-10.4653-6.6272-11.6861-3.4528-1.3279,3.4528,7.4365,11.4206,13.8108,9.5614Z"
                                    style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 683.469px 317.187px;"
                                    id="el1o8kyec2ma1" class="animable"></path>
                                <line x1="690.42059" y1="322.3545" x2="682.16559" y2="315.9309"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 686.293px 319.143px;"
                                    id="elf9xzpfw6qei" class="animable"></line>
                            </g>
                            <g id="ela9yof6ykq0n" class="animable" style="transform-origin: 685.572px 279.128px;">
                                <line x1="694.67" y1="287.2223" x2="688.56129" y2="279.2546"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 691.616px 283.238px;"
                                    id="elbj7vfhvyb2w" class="animable"></line>
                                <path
                                    d="M690.42059,281.6449s.5311-3.1873-2.1247-6.1088c-1.5981-1.7576-10.4653-6.6271-11.6861-3.4526-1.3279,3.4526,7.4365,11.4205,13.8108,9.5614Z"
                                    style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 683.469px 276.478px;"
                                    id="elxj52pcbcf58" class="animable"></path>
                                <line x1="690.42059" y1="281.6449" x2="682.16559" y2="275.2215"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 686.293px 278.433px;"
                                    id="elh7uhphlso3" class="animable"></line>
                            </g>
                            <g id="eldz9y55xgjcp" class="animable" style="transform-origin: 685.572px 252.027px;">
                                <line x1="694.67" y1="260.1219" x2="688.56129" y2="252.1543"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 691.616px 256.138px;"
                                    id="elki4n91eyez" class="animable"></line>
                                <path
                                    d="M690.42059,254.5445s.5311-3.1871-2.1247-6.1087c-1.5981-1.7576-10.4653-6.6272-11.6861-3.4527-1.3279,3.4527,7.4365,11.4205,13.8108,9.5614Z"
                                    style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 683.469px 249.377px;"
                                    id="elmpfjj15j96m" class="animable"></path>
                                <line x1="690.42059" y1="254.5445" x2="682.16559" y2="248.1211"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 686.293px 251.333px;"
                                    id="elpxox409vgeb" class="animable"></line>
                            </g>
                            <g id="elsl8w8sa5ee" class="animable" style="transform-origin: 685.572px 224.809px;">
                                <line x1="694.67" y1="232.9033" x2="688.56129" y2="224.9356"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 691.616px 228.919px;"
                                    id="elwzyuemnmwk" class="animable"></line>
                                <path
                                    d="M690.42059,227.3257s.5311-3.187-2.1247-6.1086c-1.5981-1.7576-10.4653-6.6271-11.6861-3.4528-1.3279,3.4528,7.4365,11.4207,13.8108,9.5614Z"
                                    style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 683.469px 222.159px;"
                                    id="el5t0xjnf76tg" class="animable"></path>
                                <line x1="690.42059" y1="227.3257" x2="682.16559" y2="220.9024"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 686.293px 224.114px;"
                                    id="eloioex68hjxi" class="animable"></line>
                            </g>
                            <g id="elune6jb5ara" class="animable" style="transform-origin: 703.633px 365.1px;">
                                <line x1="694.53519" y1="373.1944" x2="700.64379" y2="365.2267"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 697.59px 369.211px;"
                                    id="elpxlj11ne98l" class="animable"></line>
                                <path
                                    d="M698.78459,367.6171s-.5309-3.1873,2.1249-6.1088c1.5979-1.7578,10.4649-6.6271,11.6858-3.4526,1.328,3.4526-7.4365,11.4204-13.8107,9.5614Z"
                                    style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 705.736px 362.45px;"
                                    id="elgb1zxhdohys" class="animable"></path>
                                <line x1="698.78459" y1="367.6171" x2="707.528" y2="360.8479"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 703.156px 364.232px;"
                                    id="elksao9wqilba" class="animable"></line>
                            </g>
                            <g id="el66od930w2ib" class="animable" style="transform-origin: 703.633px 309.953px;">
                                <line x1="694.53519" y1="318.0469" x2="700.64379" y2="310.0792"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 697.59px 314.063px;"
                                    id="eljxo4hbaqcnn" class="animable"></line>
                                <path
                                    d="M698.78459,312.4695s-.5309-3.1871,2.1249-6.1086c1.5979-1.7576,10.4649-6.6271,11.6858-3.4527,1.328,3.4527-7.4365,11.4205-13.8107,9.5613Z"
                                    style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 705.736px 307.303px;"
                                    id="elr6p6vgq3t8" class="animable"></path>
                                <line x1="698.78459" y1="312.4695" x2="707.528" y2="305.7004"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 703.156px 309.085px;"
                                    id="elkuj01c7dc9p" class="animable"></line>
                            </g>
                            <g id="elqhtxfai32si" class="animable" style="transform-origin: 703.633px 282.734px;">
                                <line x1="694.53519" y1="290.8283" x2="700.64379" y2="282.8606"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 697.59px 286.844px;"
                                    id="eld3h121tiw1" class="animable"></line>
                                <path
                                    d="M698.78459,285.2509s-.5309-3.1873,2.1249-6.1087c1.5979-1.7575,10.4649-6.6272,11.6858-3.4527,1.328,3.4527-7.4365,11.4205-13.8107,9.5614Z"
                                    style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 705.736px 280.084px;"
                                    id="ele6dxr2ajkei" class="animable"></path>
                                <line x1="698.78459" y1="285.2509" x2="707.528" y2="278.4818"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 703.156px 281.866px;"
                                    id="elyn3pvzk2blj" class="animable"></line>
                            </g>
                            <g id="elfdgutiu6rzr" class="animable" style="transform-origin: 703.633px 246.758px;">
                                <line x1="694.53519" y1="254.8522" x2="700.64379" y2="246.8845"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 697.59px 250.868px;"
                                    id="eliba0peewxz" class="animable"></line>
                                <path
                                    d="M698.78459,249.2748s-.5309-3.1871,2.1249-6.1085c1.5979-1.7578,10.4649-6.6272,11.6858-3.4528,1.328,3.4528-7.4365,11.4205-13.8107,9.5613Z"
                                    style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 705.736px 244.108px;"
                                    id="el8lrccjr5b48" class="animable"></path>
                                <line x1="698.78459" y1="249.2748" x2="707.528" y2="242.5057"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 703.156px 245.89px;"
                                    id="elzpzpmdwlx3" class="animable"></line>
                            </g>
                            <g id="el3dmhjx9c48v" class="animable" style="transform-origin: 698.995px 210.294px;">
                                <line x1="694.53519" y1="226.5686" x2="700.64379" y2="218.6008"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 2px; transform-origin: 697.59px 222.585px;"
                                    id="eliggsy600dij" class="animable"></line>
                                <path
                                    d="M698.78459,220.991s-.5309-3.1869,2.1249-6.1085c1.5979-1.7578,10.4649-6.6273,11.6858-3.4527,1.328,3.4527-7.4365,11.4204-13.8107,9.5612Z"
                                    style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 705.736px 215.824px;"
                                    id="elvy931tm5uyj" class="animable"></path>
                                <line x1="698.78459" y1="220.991" x2="707.528" y2="214.222"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 703.156px 217.606px;"
                                    id="elj4n970pbok" class="animable"></line>
                                <path
                                    d="M694.16939,208.4467s-.5312-3.1871,2.1248-6.1087c1.59781-1.7575,10.4649-6.627,11.6859-3.4525,1.3278,3.4525-7.4365,11.4206-13.8107,9.5612Z"
                                    style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 701.12px 203.28px;"
                                    id="elmkw06jy74gf" class="animable"></path>
                                <line x1="694.16939" y1="208.4467" x2="702.91269" y2="201.6777"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 698.541px 205.062px;"
                                    id="ell8tubl34ih" class="animable"></line>
                                <path
                                    d="M694.596,208.8227s-3.22551,.1941-5.85231-2.7536c-1.5804-1.7735-5.4933-11.1024-2.208-11.9836,3.5728-.9588,10.5775,8.5932,8.06031,14.7372Z"
                                    style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 690.195px 201.422px;"
                                    id="eli3omxfkl1ol" class="animable"></path>
                                <line x1="694.596" y1="208.8227" x2="688.78089" y2="199.4176"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 691.688px 204.12px;"
                                    id="el8n3qkzb8beo" class="animable"></line>
                            </g>
                        </g>
                        <polygon
                            points="702.11389 438.2031 682.57339 438.2031 680.13089 388.8534 704.55639 388.8534 702.11389 438.2031"
                            style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 692.344px 413.528px;"
                            id="elc44zasyzye5" class="animable"></polygon>
                    </g>
                </g>
                <g id="freepik--Floor--inject-25" class="animable" style="transform-origin: 370.048px 438.203px;">
                    <line x1="14.3744" y1="438.2031" x2="725.72178" y2="438.2031"
                        style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 370.048px 438.203px;"
                        id="elldw6d5xkvwh" class="animable"></line>
                </g>
                <g id="freepik--Clock--inject-25" class="animable" style="transform-origin: 645.377px 96.444px;">
                    <g id="elifnkxz0w79n">
                        <g style="opacity: 0.5; transform-origin: 645.377px 96.444px;" class="animable"
                            id="elbwi08z9y8i">
                            <circle cx="645.37659" cy="96.444" r="35.5648"
                                style="fill: none; stroke: rgb(0, 0, 0); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 645.377px 96.444px;"
                                id="elv8q8egwqad8" class="animable"></circle>
                            <circle cx="645.37659" cy="96.444" r="30.2097"
                                style="fill: none; stroke: rgb(0, 0, 0); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 645.377px 96.444px;"
                                id="eldln4rt69ltf" class="animable"></circle>
                            <circle cx="645.37659" cy="96.444" r="1.7456"
                                style="fill: none; stroke: rgb(0, 0, 0); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 645.377px 96.444px;"
                                id="elljynvlzog3" class="animable"></circle>
                            <polyline points="645.37659 83.891 645.37659 96.444 649.43289 72.936"
                                style="fill: none; stroke: rgb(0, 0, 0); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 647.405px 84.69px;"
                                id="elnzse381ftsi" class="animable"></polyline>
                        </g>
                    </g>
                </g>
                <g id="freepik--Windows--inject-25" class="animable" style="transform-origin: 313.149px 140.874px;">
                    <g id="elppz613uni1">
                        <g style="opacity: 0.5; transform-origin: 172.993px 140.874px;" class="animable"
                            id="elmjn7ska9pi">
                            <rect x="52.3465" y="56.8002" width="241.2923" height="168.148"
                                style="fill: none; stroke: rgb(0, 0, 0); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 172.993px 140.874px;"
                                id="ely05oz0b06f" class="animable"></rect>
                            <rect x="58.5425" y="61.118" width="228.9003" height="159.5124"
                                style="fill: none; stroke: rgb(0, 0, 0); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 172.993px 140.874px;"
                                id="elp868w6ga2dk" class="animable"></rect>
                            <path
                                d="M287.455,100.2718v-3.7838h-48.4168V61.1631h-4.7636v35.3249h-56.01V61.1631h-4.7636v35.3249h-56.00996V61.1631h-4.76354v35.3249H58.6091v3.7838h54.1184v37.8387H58.6091v3.7838h54.1184v37.8386H58.6091v3.7838h54.1184v36.875h4.76354v-36.875h56.00996v36.875h4.7636v-36.875h56.01v36.875h4.7636v-36.875h48.4168v-3.7838h-48.4168v-37.8386h48.4168v-3.7838h-48.4168v-37.8387h48.4168Zm-169.96396,0h56.00996v37.8387h-56.00996v-37.8387Zm0,79.4611v-37.8386h56.00996v37.8386h-56.00996Zm116.78356,0h-56.01v-37.8386h56.01v37.8386Zm0-41.6224h-56.01v-37.8387h56.01v37.8387Z"
                                style="fill: none; stroke: rgb(0, 0, 0); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 173.032px 140.777px;"
                                id="el2k17la1txnm" class="animable"></path>
                        </g>
                    </g>
                    <g id="elzokws437kp9">
                        <g style="opacity: 0.5; transform-origin: 453.304px 140.874px;" class="animable"
                            id="el8ih5jihai79">
                            <rect x="332.65829" y="56.8002" width="241.29239" height="168.148"
                                style="fill: none; stroke: rgb(0, 0, 0); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 453.304px 140.874px;"
                                id="elfqw3jib0g5n" class="animable"></rect>
                            <rect x="338.85439" y="61.118" width="228.90029" height="159.5124"
                                style="fill: none; stroke: rgb(0, 0, 0); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 453.305px 140.874px;"
                                id="el95o4uqfbist" class="animable"></rect>
                            <path
                                d="M567.76679,100.2718v-3.7838h-48.4167V61.1631h-4.7637v35.3249h-56.0099V61.1631h-4.7636v35.3249h-56.01V61.1631h-4.7636v35.3249h-54.11829v3.7838h54.11829v37.8387h-54.11829v3.7838h54.11829v37.8386h-54.11829v3.7838h54.11829v36.875h4.7636v-36.875h56.01v36.875h4.7636v-36.875h56.0099v36.875h4.7637v-36.875h48.4167v-3.7838h-48.4167v-37.8386h48.4167v-3.7838h-48.4167v-37.8387h48.4167Zm-169.9639,0h56.01v37.8387h-56.01v-37.8387Zm0,79.4611v-37.8386h56.01v37.8386h-56.01Zm116.7835,0h-56.0099v-37.8386h56.0099v37.8386Zm0-41.6224h-56.0099v-37.8387h56.0099v37.8387Z"
                                style="fill: none; stroke: rgb(0, 0, 0); stroke-miterlimit: 10; stroke-width: 0.5px; transform-origin: 453.344px 140.777px;"
                                id="elgbebhj4jfrv" class="animable"></path>
                        </g>
                    </g>
                </g>
                <g id="freepik--Character_4--inject-25" class="animable" style="transform-origin: 584.891px 300.985px;">
                    <g id="el11ocpmydmsz" class="animable" style="transform-origin: 584.891px 300.985px;">
                        <path d="M541.694,307.5432v38.2019c0,3.9582,3.20879,7.1669,7.16689,7.1669h7.8725"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 549.214px 330.228px;"
                            id="elgl0h2quoawv" class="animable"></path>
                        <g id="elptkvjp89lwi" class="animable" style="transform-origin: 604.176px 360.056px;">
                            <polyline points="617.016 315.3136 614.60469 353.2233 617.362 437.1326"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 615.983px 376.223px;"
                                id="elg04sag8fht7" class="animable"></polyline>
                            <polyline points="657.37169 315.3136 654.96039 353.2233 657.71759 437.1326"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 656.339px 376.223px;"
                                id="elsg8zl15bwp9" class="animable"></polyline>
                            <polyline points="654.96039 353.2233 603.57589 353.2233 593.04829 436.6313"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 624.004px 394.927px;"
                                id="el5dey8s07thu" class="animable"></polyline>
                            <polyline points="607.58639 353.2233 556.20179 353.2233 545.67419 436.6313"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 576.63px 394.927px;"
                                id="elugafiow196" class="animable"></polyline>
                            <rect x="554.44719" y="349.1522" width="100.5132" height="4.0711"
                                style="fill: rgb(194, 194, 194); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 604.704px 351.188px;"
                                id="elo5cfev9gjn8" class="animable"></rect>
                            <rect x="603.57589" y="349.1522" width="51.3845" height="4.0711"
                                style="fill: rgb(133, 133, 133); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 629.268px 351.188px;"
                                id="el65ij2aqdc8o" class="animable"></rect>
                            <path
                                d="M657.922,315.3136h-42.458c-1.76751,0-3.12661-1.3894-3.03561-3.1032l1.3872-26.1284c.091-1.7138,1.5976-3.1031,3.3652-3.1031h42.4579c1.7675,0,3.1266,1.3893,3.0356,3.1031l-1.3872,26.1284c-.091,1.7138-1.5976,3.1032-3.36509,3.1032Z"
                                style="fill: rgb(194, 194, 194); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 637.551px 299.146px;"
                                id="el0q7otkfnsbx" class="animable"></path>
                        </g>
                        <g id="elo40wirlybp" class="animable" style="transform-origin: 586.426px 300.985px;">
                            <path
                                d="M571.54289,187.2838s.8737-3.6695,.1747-5.4169-7.1644-1.5727-8.38759-1.0485c-1.22321,.5242-2.27161,6.9897-2.27161,6.9897,0,0,5.0675-.8737,10.4845-.5243Z"
                                style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 566.524px 184.133px;"
                                id="eluqoejxmet1" class="animable"></path>
                            <path
                                d="M593.38549,230.7944l-23.93949-12.0571s3.66949-27.9586,2.09689-31.4535c-1.5727-3.4948-8.9118-2.6211-10.3098-1.7474-1.3979,.8737-12.05709,41.5885-12.05709,45.0833s33.89979,33.7251,33.89979,33.7251c0,0,5.94121,23.2405,7.1644,30.5797,1.2232,7.3391,1.0484,16.9499,.6989,18.872-.3494,1.9222-1.92209,3.2454-1.0484,4.8181,.8737,1.5727,38.26831,5.7665,47.7044,4.718,9.436-1.0484,11.3581-2.6211,11.3581-4.3685s.5243-10.934,.5243-10.934c0,0,1.7474-36.1715-1.9222-45.6075s-6.1159-12.0572-11.8824-15.3772c-5.7665-3.3201-15.552-6.116-15.552-6.116l-26.7354-10.135Z"
                                style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 599.535px 254.127px;"
                                id="elqsnvu60t1pj" class="animable"></path>
                            <path
                                d="M597.05509,249.6665s-.699,5.417-.699,6.4654c0,1.0485,.5242,5.417,.5242,5.417l7.1644-3.6696,5.9412,9.6108s11.8824-11.0087,13.45511-19.0468c1.57269-8.0381,1.74739-9.2613-.52421-11.3582s-8.21279-3.1453-8.21279-3.1453l-17.64891,15.7267Z"
                                style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 610.49px 250.715px;"
                                id="el2xftetpas0y" class="animable"></path>
                            <g id="el6litap6xka" class="animable" style="transform-origin: 596.104px 228.383px;">
                                <path
                                    d="M580.27989,213.6698s-1.7474,5.417-1.9221,9.436c-.17479,4.0191,1.5726,8.3876,1.22321,10.6592-.34951,2.2717-3.14541,3.4949-2.62121,4.8928,.5243,1.3979,4.0191,2.7958,4.0191,2.7958,0,0,5.5917,7.6887,6.6402,8.7371,1.0484,1.0485,2.9706,3.8443,5.0675,3.8443s7.3391-2.7958,7.3391-2.7958c0,0,3.84431,9.6107,5.0675,8.737,1.2232-.8737,7.5139-7.6886,10.135-13.4551,2.6211-5.7664,2.79581-7.5138,2.79581-7.5138l-2.09691-4.0191-1.92209-4.5433s4.36849-6.2906,2.79589-11.1834c-1.5727-4.8928-9.2613-12.5814-21.1437-13.2803-11.8825-.699-13.9793,6.1159-15.3773,7.6886Z"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 597.459px 232.982px;"
                                    id="elirh3wsf4wd" class="animable"></path>
                                <path
                                    d="M588.84719,199.0735s-13.3135,1.2327-12.3273,2.712,3.6982,2.9586,3.6982,2.9586c0,0-5.9172,1.2327-7.8895,4.1913-1.9724,2.9585-1.2328,3.9447-.4931,3.9447,.7396,0,4.4378-.7396,4.4378-.7396,0,0-2.2189,3.4516,.2466,4.4378,2.4654,.9862,9.8619-.9862,9.8619-.9862,0,0-.7397,1.4793,0,2.7121,.7396,1.2327,5.6705-.4931,5.6705-.4931,0,0,2.219,4.9309,3.9448,4.9309s2.712-.2465,2.712-.2465c0,0-.49309,7.1498,.4931,5.6705,.9862-1.4792,2.9586-4.1913,4.1913-4.6844,1.2327-.493,4.931,2.219,3.9448,6.9034s-2.7121,5.424-2.7121,5.424c0,0,4.6844,2.9586,6.65681,2.9586s2.46549-3.6982,2.46549-3.6982c0,0,2.712,2.9585,3.2051,1.9723,.4931-.9861,0-4.9309,0-4.9309,0,0,4.1913-5.1775,4.1913-11.0946s-4.1913-17.0118-9.6153-19.9703c-5.4241-2.9586-11.5878-6.9034-22.6824-1.9724Z"
                                    style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 596.104px 217.751px;"
                                    id="el5f4az70yful" class="animable"></path>
                                <polygon
                                    points="600.02569 251.2392 605.61739 248.0938 602.47209 256.8309 600.02569 251.2392"
                                    style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 602.822px 252.462px;"
                                    id="eldoy8s720oam" class="animable"></polygon>
                                <path
                                    d="M584.47369,241.8031s4.0191,.1748,5.9412-4.1938c0,0,2.0969,4.1938,.1748,6.6402-1.9222,2.4464-6.116-2.4464-6.116-2.4464Z"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 587.937px 241.272px;"
                                    id="elnlp605jgf8i" class="animable"></path>
                                <path
                                    d="M584.64849,231.3186c0,1.1581-.4303,2.0969-.9611,2.0969s-.9611-.9388-.9611-2.0969c0-1.158,.4303-2.0968,.9611-2.0968s.9611,.9388,.9611,2.0968Z"
                                    style="fill: rgb(38, 50, 56); transform-origin: 583.687px 231.319px;"
                                    id="elapyeo5fsso8" class="animable"></path>
                                <path d="M581.32839,226.7754s1.9221-2.9706,4.5432-.5242"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 583.6px 226.03px;"
                                    id="eli5n4bwxx7y" class="animable"></path>
                            </g>
                            <g id="elb4mawcgj2mt" class="animable" style="transform-origin: 568.523px 172.507px;">
                                <path
                                    d="M571.44469,182.9584s3.0664-5.2445,3.885-7.8823c.8187-2.6378,1.0916-10.2784,.6368-11.0061-.4548-.7276-14.1897,2.365-15.3722,3.4565-1.1824,1.0915,.3639,7.5496,1.0915,9.1869,.7277,1.6372,1.0006,2.8197,1.0006,2.8197l.3707,2.3768s6.7766-1.0748,8.3876,1.0485Z"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 568.174px 173.459px;"
                                    id="el1bp8hc65bec" class="animable"></path>
                                <path
                                    d="M575.05689,164.6158s-.091-2.274-.8187-2.5469c-.7276-.2729-13.7348,3.6384-14.0987,4.0932-.3638,.4548,.9096,4.1841,1.5463,4.2751,.6368,.0909,12.2796-1.9102,13.00721-2.4559,.72769-.5458,.36389-3.3655,.36389-3.3655Z"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 567.612px 166.248px;"
                                    id="elrblk99y3xwf" class="animable"></path>
                                <line x1="570.41789" y1="163.0695" x2="571.41849" y2="168.709"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 570.918px 165.889px;"
                                    id="elleox7h7sgq" class="animable"></line>
                                <line x1="566.23379" y1="164.07" x2="567.32529" y2="169.6185"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 566.78px 166.844px;"
                                    id="elbqh4ya5eprn" class="animable"></line>
                                <line x1="562.77739" y1="165.4344" x2="563.95979" y2="170.0733"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 563.369px 167.754px;"
                                    id="elm6z8yhro9q" class="animable"></line>
                                <path
                                    d="M569.32639,175.6219s-.8186-.6367-1.0005-2.365c-.18189-1.7282,4.27511-4.7299,4.27511-4.7299l-2.274-.1819s-4.36611-.8186-4.72991-1.6372c-.3638-.8187,9.4598-2.0921,9.4598-2.0921,0,0,2.00111,.0909,1.91011,1.4553-.09091,1.3644-1.63731,8.4593-2.36491,9.8236-.7277,1.3644-1.2735,2.365-1.2735,2.365"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 571.279px 171.438px;"
                                    id="ellwszhheis2a" class="animable"></path>
                            </g>
                            <g id="elgja6tyu2f3" class="animable" style="transform-origin: 577.303px 294.455px;">
                                <polygon
                                    points="566.58549 304.6585 564.03809 305.1583 565.35969 303.1258 589.34129 283.751 590.56709 285.2837 566.58549 304.6585"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 577.303px 294.455px;"
                                    id="el3iqourpklpa" class="animable"></polygon>
                                <polygon
                                    points="588.16109 287.2275 590.56709 285.2837 589.34129 283.751 587.03379 285.6152 588.16109 287.2275"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 588.8px 285.489px;"
                                    id="eltg3wqm2fwz" class="animable"></polygon>
                            </g>
                            <g id="eldmzb0frly4f" class="animable" style="transform-origin: 583.287px 297.296px;">
                                <path
                                    d="M596.34309,299.0407l-5.128-3.176s-8.1535-7.5184-9.3326-8.2172c-1.1791-.6989-2.7826,.8544-2.9822,1.1913-.1997,.3369-4.6297,5.8142-5.32229,6.9059-.69261,1.0918-3.78721,3.8553-3.29441,4.3295s1.6782-.1434,2.6827-.6861,2.3085-1.5907,2.3085-1.5907c0,0,1.8275,5.1347,2.3827,5.9645s5.4524,3.5503,6.3446,3.3508c.8922-.1996,1.2291-1.229,.8361-1.8716-.3929-.6426-.3617-1.0794-.3617-1.0794l2.1712-.8982s2.4828,2.109,3.9739,2.1278,2.9697-1.0167,2.9697-1.0167l2.4455,1.4039,.3063-6.7378Z"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 583.287px 297.296px;"
                                    id="elgqw60s53w6n" class="animable"></path>
                                <path
                                    d="M581.28209,305.8647s-1.8652-2.1526-1.6218-3.1008c.2433-.9483,6.5076-7.5235,6.5076-7.5235"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 582.903px 300.553px;"
                                    id="elv3fpo556wu" class="animable"></path>
                                <path
                                    d="M577.13979,302.408s-.7485-1.8093-.2681-2.3894c.4805-.5802,6.2332-7.3675,6.2332-7.3675"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 579.908px 297.53px;"
                                    id="elgfp0o689zqe" class="animable"></path>
                                <path
                                    d="M575.27479,297.7974s-.07479-1.4099,.3121-1.909c.3868-.4991,5.553-6.4505,5.553-6.4505"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 578.204px 293.618px;"
                                    id="elv477x4ijhi" class="animable"></path>
                            </g>
                            <path
                                d="M631.22629,268.8968s-1.9806,10.7592-2.2822,14.9769-1.19209,16.6699-1.19209,16.6699c0,0-26.89841-2.1254-27.18541-.9347-.287,1.1906-1.0765,6.5846,.2432,7.8902s26.2093,8.9397,33.3534,10.6618c7.1441,1.7221,11.5631-3.6142,11.5631-3.6142,0,0,5.0236-30.7286,4.63671-36.6104-.38681-5.8819-.61571-13.973-2.49461-18.7503-1.8789-4.7774-7.8465-8.8378-10.4431-9.2253"
                                style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 625.227px 284.232px;"
                                id="el710wru65d5g" class="animable"></path>
                            <path
                                d="M600.56659,299.6089s-3.924-1.0735-4.2235-.5682-1.06109,6.2449-.3063,6.7378c.7549,.493,4.2486,.7428,4.2486,.7428,0,0,.7925-4.7662,.2812-6.9124Z"
                                style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 598.226px 302.713px;"
                                id="elc3goh3l4wpu" class="animable"></path>
                            <path
                                d="M590.70869,317.3797l-7.2283,4.3466s-24.2952,0-36.1416,2.6102c-11.8464,2.6103-16.4645,6.4252-16.6653,9.8386s8.2323,86.7398,8.2323,86.7398c0,0,0,1.0039-4.6181,4.6181-4.6181,3.6141-11.2441,8.0314-12.0472,9.8385-.8032,1.8071,4.21651,2.4095,8.8346,1.8071,1.6052-.2094,12.8503-6.0236,12.8503-6.0236,0,0,9.437-1.2047,10.8425-3.4134,1.4055-2.2086,0-4.8188,0-4.8188,0,0-2.4094-4.0158-2.4094-5.6221s2.0887-67.5462,2.0887-68.1485c0-.6024,92.8833-.3197,92.8833-.3197,0,0,4.21651-6.2244,3.2126-12.248-1.0039-6.0236-3.0118-17.5985-3.0118-17.5985l-56.8226-1.6063Z"
                                style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 586.426px 377.392px;"
                                id="el3q53rywpd43" class="animable"></path>
                            <path
                                d="M554.76789,427.7416c1.4055-2.2086,0-4.8189,0-4.8189,0,0-1.9928-3.3255-2.3496-5.1335-4.3199,1.4785-13.5125,3.1257-13.5125,3.1257,0,0,0,1.0039-4.6181,4.6181-4.6182,3.6142-11.2441,8.0314-12.0472,9.8385-.8032,1.8071,4.21651,2.4095,8.8346,1.8071,1.6052-.2094,12.8503-6.0236,12.8503-6.0236,0,0,9.437-1.2047,10.8425-3.4134Z"
                                style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 538.774px 427.597px;"
                                id="elmm5lgiwba9c" class="animable"></path>
                            <path
                                d="M568.62219,413.6866s-4.4173,8.8346-7.2283,12.4487c-2.8111,3.6142-3.815,9.2362-2.4095,11.4449,1.4055,2.2086,6.2244,3.8149,10.6417,.2008,4.4173-3.6142,8.8346-22.8897,8.8346-22.8897l-9.8385-1.2047Z"
                                style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 568.407px 426.801px;"
                                id="eljgwhkf2fyhe" class="animable"></path>
                            <path
                                d="M638.29509,332.7696s-20.2794-6.8268-34.5353-7.8307c-14.25579-1.0039-20.88179,.6024-21.4841,2.811-.6024,2.2087-11.4449,66.4604-11.4449,66.4604,0,0-5.0196,20.681-3.6141,21.6849,1.4055,1.004,6.8267,1.8071,9.8385,2.2087,3.01181,.4016,8.433-13.2519,10.0393-16.6653s10.2402-26.303,13.65351-36.1416c3.41339-9.8385,8.03149-22.6889,8.03149-22.6889"
                                style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 602.632px 371.373px;"
                                id="elmbohwixso4r" class="animable"></path>
                        </g>
                        <g id="elhb7fg1l9gbi" class="animable" style="transform-origin: 549.189px 307.323px;">
                            <rect x="507.10339" y="305.2873" width="84.171" height="4.0712"
                                style="fill: rgb(194, 194, 194); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 549.189px 307.323px;"
                                id="el13glghexfvx" class="animable"></rect>
                            <rect x="555.22949" y="305.2873" width="36.0448" height="4.0712"
                                style="fill: rgb(133, 133, 133); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 573.252px 307.323px;"
                                id="elah9a39rb78" class="animable"></rect>
                        </g>
                        <g id="elj4sqe1bgdxo" class="animable" style="transform-origin: 539.522px 303.298px;">
                            <path
                                d="M533.58879,304.6637c-.1163-.2748-.1806-.577-.1806-.8942,0-1.2688,1.0286-2.2974,2.2974-2.2974s2.2973,1.0286,2.2973,2.2974c0,.2411-.0371,.4736-.106,.692"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 535.706px 303.068px;"
                                id="eld8i11c24idt" class="animable"></path>
                            <path
                                d="M536.52429,304.6637c-.11629-.2748-.1806-.577-.1806-.8942,0-1.2688,1.0286-2.2974,2.2974-2.2974s2.2973,1.0286,2.2973,2.2974c0,.2411-.0371,.4736-.106,.692"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 538.641px 303.068px;"
                                id="elar8kyhkhbh8" class="animable"></path>
                            <path
                                d="M539.45979,304.6637c-.1163-.2748-.1806-.577-.1806-.8942,0-1.2688,1.0286-2.2974,2.2974-2.2974s2.2973,1.0286,2.2973,2.2974c0,.2411-.0371,.4736-.106,.692"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 541.577px 303.068px;"
                                id="elqk50jw1uibg" class="animable"></path>
                            <path
                                d="M542.39529,304.6637c-.11629-.2748-.1806-.577-.1806-.8942,0-1.2688,1.0286-2.2974,2.2974-2.2974s2.2973,1.0286,2.2973,2.2974c0,.2411-.0371,.4736-.106,.692"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 544.512px 303.068px;"
                                id="el1tr1ph7o4pf" class="animable"></path>
                            <path
                                d="M545.33079,304.6637c-.1163-.2748-.1806-.577-.1806-.8942,0-1.2688,1.0286-2.2974,2.2974-2.2974s2.2973,1.0286,2.2973,2.2974c0,.2411-.0371,.4736-.106,.692"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 547.448px 303.068px;"
                                id="elkfo46boxkk8" class="animable"></path>
                            <path
                                d="M548.26629,304.6637c-.11629-.2748-.1806-.577-.1806-.8942,0-1.2688,1.0286-2.2974,2.2974-2.2974s2.2973,1.0286,2.2973,2.2974c0,.2411-.0371,.4736-.106,.692"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 550.383px 303.068px;"
                                id="el6m9ymvlkoy3" class="animable"></path>
                            <path
                                d="M551.20179,304.6637c-.1163-.2748-.1806-.577-.1806-.8942,0-1.2688,1.0286-2.2974,2.2974-2.2974s2.2973,1.0286,2.2973,2.2974c0,.2411-.0371,.4736-.106,.692"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 553.319px 303.068px;"
                                id="el5wz0f9dbvug" class="animable"></path>
                            <g id="el6r0egh72grv" class="animable" style="transform-origin: 539.522px 304.351px;">
                                <rect x="536.06579" y="303.5776" width="35.8753" height="1.5464"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 554.003px 304.351px;"
                                    id="el8z3zj2fiwc7" class="animable"></rect>
                                <rect x="507.10339" y="303.5776" width="27.3629" height="1.5464"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 520.785px 304.351px;"
                                    id="elt45cuxytktd" class="animable"></rect>
                            </g>
                        </g>
                    </g>
                </g>
                <g id="freepik--Backpack_2--inject-25" class="animable" style="transform-origin: 653.313px 406.14px;">
                    <g id="el9p5sx3rl3b" class="animable" style="transform-origin: 653.313px 406.14px;">
                        <path
                            d="M623.54579,437.7179s.2823-.0297,4.10521-1.213c3.823-1.1833,9.83039-4.005,9.83039-4.005l-4.7331-4.278s-4.187,2.2755-6.0985,3.5498-3.0037,1.7294-4.005,2.3666c-1.0012,.6371,.901,3.5796,.901,3.5796Z"
                            style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 629.918px 432.97px;"
                            id="ely52x9ztyh4" class="animable"></path>
                        <path
                            d="M660.10649,374.4089c-.0067,.002-13.2234,3.8705-21.60549,7.4168-8.38421,3.5472-7.42391,11.9414-7.10141,13.8763,.32241,1.9348,.652,15.4684,.652,21.9178s-1.56559,14.9158,.3692,16.8506c1.9348,1.9348,8.3375,3.1425,11.8847,3.465,3.5471,.3224,32.5694-.3225,36.11651-.645,3.54719-.3224,9.02919-2.5797,10.31909-7.0943,1.2898-4.5146,1.9348-17.7358,.9674-20.3156-.9669-2.5782-11.9224-16.4445-16.12349-21.2829-2.78521-3.2089-9.75821-15.863-15.47851-14.1887Z"
                            style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 661.684px 406.14px;"
                            id="elbfmduyqk5z" class="animable"></path>
                        <path
                            d="M690.741,430.1961c1.28989-4.5145,1.93489-17.7358,.96739-20.3156-.8351-2.2271-9.12039-12.8727-14.0703-18.8508l-1.0857-.1749s-7.6457-4.9621-16.22339-2.3411c-8.57771,2.621-10.00731,11.1987-8.57771,16.6789,1.4296,5.4803,8.816,12.8666,7.8629,19.2999s-3.5741,10.9604-3.8123,12.1518c-.05089,.2545,.0447,.7334,.2184,1.3183,9.7332-.1356,22.15871-.4682,24.40171-.6722,3.54719-.3224,9.02919-2.5797,10.319-7.0943Z"
                            style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 671.721px 412.856px;"
                            id="elgxkslwy41eb" class="animable"></path>
                        <path
                            d="M648.49759,379.246s-10.964,3.2246-13.5437,9.0291c-2.5798,5.8044-.645,11.2864,.3224,12.8988,.9674,1.6123,2.5798,9.3516,2.5798,12.5763s-1.2899,5.482,.3224,5.8044c1.6124,.3225,5.482-.3224,5.482-.3224,0,0-3.5471-16.1235-4.51449-19.6707-.96751-3.5471-1.61241-5.482,0-8.3842,1.61229-2.9022,5.15949-7.0943,8.70659-8.7067,3.5472-1.6123,12.2539-5.4819,12.2539-5.4819l2.9022-.9674s2.25731-1.9349-2.9022-1.6124c-5.15949,.3225-11.6089,4.8371-11.6089,4.8371Z"
                            style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-miterlimit: 10; transform-origin: 648.584px 397.009px;"
                            id="el57u4dj1zij" class="animable"></path>
                        <path
                            d="M653.33459,437.9354s6.44941-9.6741,4.1921-17.0909c-2.2573-7.4168-10.6415-13.2212-8.70669-23.5403,1.93489-10.319,13.54379-12.5763,19.34819-11.2864s8.3842,4.837,8.3842,4.837c0,0-11.5482-4.3473-19.6706,1.6124-4.2692,3.1324-4.8371,10.319-.3225,17.0909,4.5146,6.7718,6.7719,14.5111,4.837,20.3155-1.9348,5.8045-3.6787,7.259-3.6787,7.259l-4.383,.8028Z"
                            style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 662.543px 411.812px;"
                            id="elsv97meryso" class="animable"></path>
                        <path
                            d="M666.03659,421.932s16.4779-5.3805,23.2035-11.0973l.3363,4.0354s-11.7699,7.7344-22.1946,10.0884l-1.3452-3.0265Z"
                            style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 677.806px 417.897px;"
                            id="eljehpa4tluxc" class="animable"></path>
                        <path
                            d="M633.76229,391.9477s-1.6346,12.0555-7.9689,23.2937c-6.3343,11.2382-9.6036,16.3465-10.6252,19.2072-1.0217,2.8606-1.0641,3.0995,.613,3.2693,2.4035,.2433,7.7646,0,7.7646,0,0,0,.8173-5.517,3.2693-10.4209,2.45191-4.904,9.3992-18.3898,11.0338-28.6064,1.6347-10.2165,4.6996-18.7984,4.6996-18.7984,0,0-3.8822,1.4303-6.1299,3.0649-2.24759,1.6347-2.6563,8.9906-2.6563,8.9906Z"
                            style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 628.5px 408.859px;"
                            id="elh3253ja4g6t" class="animable"></path>
                    </g>
                </g>
                <g id="freepik--Character_2--inject-25" class="animable"
                    style="transform-origin: 268.516px 296.063px;">
                    <g id="eldnfv2e55w3" class="animable" style="transform-origin: 268.516px 296.063px;">
                        <g id="elj6tipc2ur9c" class="animable" style="transform-origin: 281.52px 360.439px;">
                            <polyline points="294.3594 315.6972 291.9481 353.6069 294.7053 437.5162"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 293.327px 376.607px;"
                                id="el81z7fjdgla" class="animable"></polyline>
                            <polyline points="334.715 315.6972 332.30369 353.6069 335.061 437.5162"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 333.682px 376.607px;"
                                id="elvf4fa3rilc" class="animable"></polyline>
                            <polyline points="332.30369 353.6069 280.9192 353.6069 270.3916 437.0149"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 301.348px 395.311px;"
                                id="elx0rp9no4bn" class="animable"></polyline>
                            <polyline points="284.9297 353.6069 233.5452 353.6069 223.0176 437.0149"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 253.974px 395.311px;"
                                id="el6fcqzuh3fnk" class="animable"></polyline>
                            <rect x="231.7905" y="349.5358" width="100.5132" height="4.0712"
                                style="fill: rgb(194, 194, 194); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 282.047px 351.571px;"
                                id="eluhchuj6e9ce" class="animable"></rect>
                            <rect x="280.9192" y="349.5358" width="51.3845" height="4.0711"
                                style="fill: rgb(133, 133, 133); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 306.611px 351.571px;"
                                id="elzdwwj02e6tm" class="animable"></rect>
                            <path
                                d="M335.26529,315.6972h-42.458c-1.7675,0-3.1266-1.3893-3.0356-3.1032l1.3872-26.1283c.091-1.7139,1.5977-3.1032,3.3652-3.1032h42.4579c1.76759,0,3.12669,1.3893,3.03569,3.1032l-1.3873,26.1283c-.091,1.7139-1.5976,3.1032-3.3651,3.1032Z"
                                style="fill: rgb(194, 194, 194); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 314.895px 299.53px;"
                                id="elglzgk3qxn1b" class="animable"></path>
                        </g>
                        <path d="M224.2708,307.9268v38.2019c0,3.9582,3.2088,7.1669,7.167,7.1669h7.8724"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 231.791px 330.611px;"
                            id="elfvhuyx6ofyb" class="animable"></path>
                        <g id="elp7d79kcsq5o" class="animable" style="transform-origin: 276.289px 296.063px;">
                            <path
                                d="M228.6451,178.9315s6.141,43.8641,7.6762,48.0312c1.5353,4.1671,24.5639,30.2663,24.5639,30.2663l.8773,19.7388-7.9768,43.373s12.0626,3.9477,16.8877,5.0443c4.825,1.0966,18.2036,2.8512,29.6083,2.6319,11.4046-.2193,21.932-5.7023,21.932-5.7023l-5.1896-57.3493s18.2368-27.0789,18.5131-27.6315c.2763-.5527,1.3816-4.2829,2.625-10.5s3.4539-39.0986,3.4539-39.0986l1.1053-10.6382c.2763-.5526,1.1053-1.3815,1.6579-3.1776,.5526-1.796,2.0724-11.0526,2.7631-12.9868,.6908-1.9342-.5526-2.0724-.5526-2.0724l-2.48679,5.6645s.82889-6.9079,.82889-8.2895c0-1.3815-.9671-2.0723-1.3816-1.1052-.4144,.9671-1.9342,8.0131-1.9342,8.0131l-1.1052,.4145s-.1382-5.9408-.2763-8.9803c-.1382-3.0394-1.38159-1.796-1.38159-1.796l-1.10531,10.3618-1.51969,.6908s.25879-5.4881,.25879-7.1953c0-1.7073-.2134-2.5609-1.2805-2.3475-1.067,.2134-1.067,5.5486-1.067,5.5486l-1.067,8.1094s-.6402-1.4938-.6402-2.5609c0-1.067-.8537-2.3474-2.7743-2.134-1.9207,.2134,.1805,1.7506,.1805,3.2444,0,1.4939,.1151,2.2459,.3285,4.38,.2134,2.134,3.5457,5.18,3.5457,5.18l.6402,2.9877s-5.3351,21.3407-6.4022,26.8892c-1.067,5.5486-5.9754,21.5541-5.9754,21.5541,0,0-5.9754,8.5363-8.3228,9.8167-2.3475,1.2805-13.5453,12.6299-13.5453,12.6299l-15.5351-.3836s-9.8695-8.9921-14.0366-12.2819c-4.167-3.2898-13.3785-9.8694-13.3785-9.8694l-2.6319-4.1671s-9.2114-22.8094-12.7205-29.6083c-3.5092-6.7989-6.799-18.4229-6.799-18.4229,0,0,4.3864-5.0444,4.6058-6.5796,.2193-1.5353,0-5.7024,.6579-7.4569,.658-1.7546-2.1932-.8773-3.0705,.6579-.8773,1.5353-1.0966,4.6057-1.0966,4.6057l-3.5091-3.5091s-4.1671-7.8955-4.8251-8.7728c-.6579-.8773-2.8511,.658-2.4125,2.1932,.4387,1.5352,2.6319,7.6762,2.6319,7.6762,0,0-5.2637-7.2376-5.9217-7.6762s-2.4125-.4386-1.7546,1.5352c.658,1.9739,4.1671,8.7729,4.1671,8.7729,0,0-5.483-3.5092-6.5796-4.3864-1.0966-.8773-2.1932,.4386-1.7545,1.3159,.4386,.8773,5.0443,5.9216,5.0443,5.9216l-4.825-.8772s-1.5353,1.7545,.6579,2.6318,5.0444,2.6318,5.0444,2.6318l6.5796,5.0444Z"
                                style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 281.596px 240.283px;"
                                id="el4iqbg2y1j59" class="animable"></path>
                            <path
                                d="M236.102,166.4302s-2.1425,1.3113-3.7977,2.1389-3.0001,3.3105-2.4829,4.552c.5173,1.2414,1.0346,1.8621,1.0346,1.8621"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 232.906px 170.707px;"
                                id="elvz3toxkfyqo" class="animable"></path>
                            <path d="M339.25519,175.8061s-.438-3.066,1.314-3.9419c1.752-.876,2.2995-1.533,2.2995-1.533"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 341.038px 173.069px;"
                                id="elzzxmju6gkym" class="animable"></path>
                            <path
                                d="M222.0954,417.6517s-8.0211,9.5862-9.9775,11.1513c-1.9563,1.5651-7.4342,3.7171-6.8473,6.0648,.5869,2.3476,6.8473,3.1302,9.195,2.7389,2.3476-.3913,11.3469-3.7171,11.3469-3.7171,0,0,10.9557-1.7608,12.9121-3.1302,1.9564-1.3695-1.1738-14.0859-1.1738-14.0859l-15.4554,.9782Z"
                                style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 222.297px 427.188px;"
                                id="eld2gdxerbbnr" class="animable"></path>
                            <path
                                d="M247.4779,415.4816s-7.6298,7.0429-12.1295,11.1513c-4.4996,4.1084-5.6735,8.608-3.5215,10.9557,2.1521,2.3476,7.043,3.1302,11.5426-.3913,4.4997-3.5215,12.9121-15.0641,12.9121-15.0641l-8.8037-6.6516Z"
                                style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 243.532px 427.539px;"
                                id="elez0nut70lod" class="animable"></path>
                            <path
                                d="M257.9071,319.3916s-3.8973,3.1179-4.0922,3.7025c-.1949,.5846-32.1533,5.6512-37.4147,7.405-5.2615,1.7538-6.8204,7.0152-6.8204,7.0152l13.8721,77.3842s-2.5332,.5846-2.5332,1.1692-.1949,2.1436-.1949,2.7282,4.2871,1.9486,6.4306,2.3384c2.1436,.3897,11.6921,.1949,11.6921,.1949l1.3276-71.733h71.9065s10.1331-4.0922,10.5229-12.4716c.3897-8.3793-2.1436-16.9535-2.1436-16.9535l-62.5528-.7795Z"
                                style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 266.112px 370.385px;"
                                id="el1zbivuglzas" class="animable"></path>
                            <path
                                d="M318.901,334.3965s-23.3842-10.7178-33.3225-10.5229c-9.9383,.1948-22.215,8.3793-22.9945,11.8869-.7795,3.5077-8.1845,54.3683-8.3794,55.1478-.1948,.7795-4.8717,17.733-4.8717,17.733l-2.923,2.7282s-.1949,3.6842-.5846,4.4637c-.3897,.7795,6.4307,8.5742,8.964,8.3793,2.5332-.1948,6.0409-4.0739,6.0409-4.0739l4.6768-7.5999s14.4203-30.3995,17.733-35.8558c3.3128-5.4563,1.9487-10.7178,4.0923-16.9536,2.1435-6.2357,4.6768-19.0971,4.6768-19.0971"
                                style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 282.355px 374.044px;"
                                id="elgsu54vxgs26" class="animable"></path>
                            <path
                                d="M317.0241,264.9654s1.8515-2.7493,4.3806-6.5091l-3.7272-25.2621c-1.5113,1.8623-3.0689,3.5906-4.0253,4.1122-2.3474,1.2805-13.5452,12.6299-13.5452,12.6299l-15.5352-.3836s-9.8693-8.9921-14.0365-12.2819c-3.0416-2.4013-8.7573-6.5456-11.6301-8.6143l-5.2365,20.2701c4.0555,4.7195,7.2165,8.3023,7.2165,8.3023l.8773,19.7389-7.9768,43.3729s12.0626,3.9478,16.8877,5.0444c4.825,1.0966,17.9322-.498,29.3369-.7174,11.4047-.2193,22.2034-2.3531,22.2034-2.3531l-5.1896-57.3492Z"
                                style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 287.941px 277.211px;"
                                id="el10ah59j635pj" class="animable"></path>
                            <line x1="317.0241" y1="264.9654" x2="312.9492" y2="273.7969"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 314.987px 269.381px;"
                                id="elrj4vplvf2n" class="animable"></line>
                            <path d="M276.7117,253.3268s-.5533,10.2351,1.6597,16.0442"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 277.511px 261.349px;"
                                id="el0585ztb2azgf" class="animable"></path>
                            <path d="M280.3752,251.5441s-.344,5.3788,.7624,7.8684"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 280.731px 255.478px;"
                                id="el9ae2aefeaed" class="animable"></path>
                            <polyline points="262.3273 243.0918 260.8852 257.2289 258.7312 247.5178"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 260.529px 250.16px;"
                                id="elchrq2a9p9al" class="animable"></polyline>
                            <path d="M282.2452,309.2678c1.0363,1.3804,2.1428,2.6685,3.3184,3.8095"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 283.904px 311.173px;"
                                id="elnmagn3p70q" class="animable"></path>
                            <path d="M271.1792,285.4151s3.1747,11.8168,9.2424,21.2266"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 275.8px 296.028px;"
                                id="elowzzfi72uwq" class="animable"></path>
                            <g id="el4thffeen7e9" class="animable" style="transform-origin: 290.378px 249.059px;">
                                <path
                                    d="M280.9598,240.8743s-.7308,8.7697-.5846,10.6698c.1461,1.9001,4.3848,5.7003,8.9158,5.7003s10.816-7.3081,10.816-7.3081l.2923-6.4311-19.4395-2.6309Z"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 290.378px 249.059px;"
                                    id="elswtfgstq89" class="animable"></path>
                                <polygon points="290.4594 245.3224 280.3752 251.5441 280.9598 240.8743 290.4594 245.3224"
                                    style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 285.417px 246.209px;"
                                    id="elh25t969qdc6" class="animable"></polygon>
                            </g>
                            <g id="elq5gus4d1syl" class="animable" style="transform-origin: 293.694px 222.086px;">
                                <path
                                    d="M275.9903,209.4496s-3.3617,2.1924-3.8002,4.531c-.4385,2.3385,.5847,5.5541,.5847,5.5541l2.4847-1.7539s-.7308,2.4847-.5846,3.9463c.1461,1.4616,1.9001,3.8002,1.9001,3.8002,0,0,2.1924-10.0851,2.3386-14.1776,.1461-4.0926,.1461-5.408,.1461-5.408l-3.0694,3.5079Z"
                                    style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 275.57px 215.734px;"
                                    id="el14djkowrgk4" class="animable"></path>
                                <path
                                    d="M277.0135,223.9196s-2.3386-1.0232-2.9233,.7308c-.5846,1.7539,.877,6.5772,2.1924,8.3312l1.3155,1.7539-.5846-10.8159Z"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 275.778px 229.179px;"
                                    id="el1u5r8x1inea" class="animable"></path>
                                <path
                                    d="M276.8673,211.7881s.2923,3.8002-.7308,11.4006c-1.0231,7.6005-1.1693,11.1083,0,13.4469s5.408,8.0389,8.6235,9.062c3.2156,1.0232,9.6467,2.7771,15.6393-2.1924s9.062-12.1314,9.939-18.4164c.877-6.2849-4.6772-14.6161-7.8927-15.7854-3.2156-1.1693-23.0936-3.0694-25.5783,2.4847Z"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 292.872px 227.436px;"
                                    id="elowzk3khbt9" class="animable"></path>
                                <path
                                    d="M309.169,209.5957s-2.4847-4.2387-8.185-8.0389c-5.7003-3.8002-13.0084-5.1156-12.4238-3.654l.5847,1.4616s-10.5237,0-13.1546,2.3386-3.8002,4.531-2.6309,4.531,2.3386,1.0231,2.3386,1.0231c0,0-5.1157,2.3386-3.5079,4.8234,1.6078,2.4847,10.6698-.4385,10.6698-.4385,0,0,1.1693,1.4616,3.5079,1.4616s12.2776-1.1693,13.593-1.1693c1.3155,0,3.9464,.7308,3.9464,.7308,0,0-2.7771,3.8002-2.3386,5.408s2.1924-.2923,2.1924-.2923c0,0-.1461,5.2618,.7308,6.5772,.877,1.3155,3.0694,3.6541,3.0694,3.6541,0,0,.877,2.9232,1.4617,2.0462,.5846-.8769,.8769-2.6309,1.0231-3.0694,.1461-.4384,2.3386,1.3155,2.3386,1.3155,0,0,1.0231-4.2387,1.1693-5.408,.1461-1.1693-.1462-3.654-.1462-3.654,0,0,1.6078,2.0462,1.7539,1.3154,.1462-.7308,1.0232-1.0231-.7308-5.5541-1.7539-4.531-2.9232-5.408-5.2618-5.408Z"
                                    style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 293.694px 213.771px;"
                                    id="elzb2vfg1spfh" class="animable"></path>
                                <path
                                    d="M287.3909,217.7808v2.1924s-3.3617,.7308-3.5078,1.6078c-.1462,.8769,2.6309,2.9232,2.6309,2.9232"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 285.634px 221.143px;"
                                    id="eld5o4zv0e90s" class="animable"></path>
                                <path
                                    d="M281.9667,228.8341s2.0207,.4538,4.1101,.6165c1.8742,.146,6.0251-1.0972,6.8845-1.4385-.7864,1.7397-2.4154,4.8622-5.1377,5.8569-3.694,1.3498-4.8294-1.3358-5.1376-2.4661-.3083-1.1302-.7193-2.5688-.7193-2.5688Z"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 287.464px 231.126px;"
                                    id="elxh6wbi21yfs" class="animable"></path>
                                <path d="M292.7989,219.0962s3.2156-1.9001,4.9695,1.0232"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 295.284px 219.304px;"
                                    id="el9dhb3ddbjif" class="animable"></path>
                                <path d="M283.5907,217.9269s-3.2155-1.1693-4.3848,1.3155"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 281.398px 218.447px;"
                                    id="el5mrxb1g406q" class="animable"></path>
                                <path d="M292.9451,213.9806s3.8002-1.4617,5.7003,3.3617"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 295.795px 215.557px;"
                                    id="eljk6tb5jbyl" class="animable"></path>
                                <path d="M283.0061,213.1036s-3.0694-.877-4.8234,2.3386"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 280.594px 214.218px;"
                                    id="elle1jftmwyqk" class="animable"></path>
                                <path
                                    d="M305.3688,229.766s2.3386-4.0925,4.6772-2.7771c2.3386,1.3155,2.0462,4.0926,.2923,6.8696-1.7539,2.7771-5.408,3.3618-5.5541,1.6078"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 308.256px 231.595px;"
                                    id="elr5cosd6a85b" class="animable"></path>
                            </g>
                        </g>
                        <g id="el6zcxwftvrer" class="animable" style="transform-origin: 231.766px 307.706px;">
                            <rect x="189.6803" y="305.6709" width="84.1709" height="4.0712"
                                style="fill: rgb(194, 194, 194); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 231.766px 307.707px;"
                                id="ela7olea56455" class="animable"></rect>
                            <rect x="237.8063" y="305.6709" width="36.0449" height="4.0712"
                                style="fill: rgb(133, 133, 133); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 255.829px 307.707px;"
                                id="elep1yy4njvr" class="animable"></rect>
                        </g>
                    </g>
                </g>
                <g id="freepik--Backpack_1--inject-25" class="animable"
                    style="transform-origin: 315.784px 403.185px;">
                    <g id="el2q1nk1l1gnr" class="animable" style="transform-origin: 315.784px 403.185px;">
                        <path
                            d="M321.1778,371.2502s-11.7276-2.0118-16.8648-.036c-5.1371,1.9758-9.0887,6.7178-9.879,9.8791-.7903,3.1613,.3951,22.9194-.3952,29.242s2.4069,25.4816,5.1731,27.0623c2.7661,1.5806,20.7178,.7488,20.7178,.7488l22.319-.5449s10.2742-6.3226,10.2742-12.6452-1.5807-13.8307-4.3468-16.5968c-2.76609-2.7661-14.621-18.1775-14.621-20.5485s-7.6353-16.1657-12.3773-16.5608Z"
                            style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 323.22px 404.355px;"
                            id="elyspatk7oc" class="animable"></path>
                        <path
                            d="M314.4383,417.6782s2.2465,7.2387,2.4961,10.9829c.2496,3.7442,5.9907,7.2388,9.7349,7.4884s9.23571-4.493,14.2279-5.9907c4.9923-1.4977,10.2341-4.2434,10.7334-6.9892,.4992-2.7457,.829-10.6497-3.4545-14.8101s-6.0308-4.1604-7.77809-3.6612c-1.74731,.4992-24.2124,13.2295-25.9597,12.9799Z"
                            style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 333.176px 420.317px;"
                            id="elw4wgh5gp7ho" class="animable"></path>
                        <path
                            d="M304.313,371.2142s-3.1042,2.5321-4.8515,6.5259,.2496,15.7256,.9984,19.4698c.7489,3.7442,1.2481,11.4822,1.2481,11.4822,0,0,3.9938,.4992,4.9923,.2496,.9984-.2496-2.4962-8.4868-3.245-16.4744-.7488-7.9877-.9984-11.7318,.4992-14.9768,1.4977-3.245,5.2419-6.2403,6.49-6.2403,1.248,0,7.738-.2496,7.738-.2496,0,0-1.7473-2.9954-5.7411-2.9954s-6.0225,1.6754-8.1284,3.209Z"
                            style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 308.473px 388.508px;"
                            id="elij1fqs1tgc" class="animable"></path>
                        <path
                            d="M294.2196,390.2207s-1.4976,20.2187-3.9938,25.4605c-2.4961,5.2419-10.2341,14.9768-10.9829,17.2233-.7489,2.2465,.7488,4.9923,2.4961,5.2419,1.7473,.2496,27.4574,0,27.4574,0l-21.7163-2.4961s3.4946-5.7411,5.2419-11.9814c1.7472-6.2404,1.3168-13.3336,1.3168-13.3336l.1808-20.1146Z"
                            style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 294.121px 414.239px;"
                            id="elw2wq9ky0r8b" class="animable"></path>
                        <path
                            d="M340.398,399.706s-8.23721,6.49-13.47911,8.4869c-5.24189,1.9969-12.4806,3.4945-13.4791,5.2418-.9984,1.7473,.9985,4.2435,.9985,4.2435,0,0,1.7473,.9984,8.4868-1.2481,6.7395-2.2465,17.9721-9.7349,19.4698-10.983l1.4977-1.248s-1.2481-5.7411-3.49459-4.4931Z"
                            style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 328.526px 408.714px;"
                            id="el4mhof4kalg7" class="animable"></path>
                        <path
                            d="M308.4476,408.9417l2.7457-.4992s-5.9907-18.9706-4.2434-26.2094c1.7473-7.2387,6.2403-7.9876,10.2341-9.4853"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 311.907px 390.845px;"
                            id="elre99ufqo9ge" class="animable"></path>
                        <path
                            d="M324.20959,382.9474c1.51941,2.0484,1.4496,4.6743-.1559,5.8652-1.6055,1.1908-4.13869,.4957-5.658-1.5527-1.5194-2.0483-1.4497-4.6742,.1558-5.8651,1.6055-1.1909,4.1387-.4958,5.6581,1.5526Z"
                            style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 321.303px 385.104px;"
                            id="elq88nhpxzi79" class="animable"></path>
                        <path
                            d="M310.9437,415.9309h-8.9861c-3.4946,0-6.7395-.7489-6.7395-.7489v5.2419s3.2449,1.2481,5.9907,1.2481,10.2341-.2497,10.2341-.2497l-.4992-5.4914Z"
                            style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 303.33px 418.427px;"
                            id="eldp2fr0z6vyl" class="animable"></path>
                        <path d="M312.4414,423.9185s.2496,4.7426,1.4976,6.9891c1.2481,2.2465,1.2481,3.245-1.7473,3.9938"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 313.459px 429.41px;"
                            id="elqeteb6rbeis" class="animable"></path>
                        <path d="M316.4352,403.9494s9.7349-.9984,17.2233-7.4883"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 325.047px 400.205px;"
                            id="elj1q1m4ony5d" class="animable"></path>
                        <path d="M310.6941,398.458s7.2387,3.9938,16.4744-.2496"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 318.931px 399.185px;"
                            id="eljkaqmwsme2" class="animable"></path>
                    </g>
                </g>
                <g id="freepik--Character_3--inject-25" class="animable"
                    style="transform-origin: 424.385px 296.488px;">
                    <g id="elwkxc307houi" class="animable" style="transform-origin: 424.385px 296.488px;">
                        <path d="M384.69409,307.2444v38.2019c0,3.9582,3.2088,7.1669,7.16691,7.1669h7.87249"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 392.214px 329.929px;"
                            id="el1wdf7v4vtp6" class="animable"></path>
                        <g id="elnfgss1nom98" class="animable" style="transform-origin: 444.672px 359.757px;">
                            <polyline points="457.51139 315.0148 455.10009 352.9245 457.85729 436.8337"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 456.479px 375.924px;"
                                id="elzttbtq986x" class="animable"></polyline>
                            <polyline points="497.86709 315.0148 495.45579 352.9245 498.213 436.8337"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 496.834px 375.924px;"
                                id="el8lznsuewqjg" class="animable"></polyline>
                            <polyline points="495.45579 352.9245 444.07129 352.9245 433.54369 436.3324"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 464.5px 394.628px;"
                                id="ely3fh7hfrr2m" class="animable"></polyline>
                            <polyline points="448.08169 352.9245 396.69719 352.9245 386.16959 436.3324"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 417.126px 394.628px;"
                                id="elduwb6s77qcb" class="animable"></polyline>
                            <rect x="394.94259" y="348.8534" width="100.5132" height="4.0711"
                                style="fill: rgb(194, 194, 194); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 445.199px 350.889px;"
                                id="elb6pw1gmysj8" class="animable"></rect>
                            <rect x="444.07129" y="348.8533" width="51.3845" height="4.0712"
                                style="fill: rgb(133, 133, 133); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 469.764px 350.889px;"
                                id="el2e5hz8k2ju" class="animable"></rect>
                            <path
                                d="M498.41729,315.0148h-42.4579c-1.7675,0-3.1266-1.3894-3.0356-3.1032l1.38721-26.1284c.091-1.7138,1.59759-3.1031,3.36509-3.1031h42.458c1.7675,0,3.1266,1.3893,3.0356,3.1031l-1.3872,26.1284c-.091,1.7138-1.5976,3.1032-3.3652,3.1032Z"
                                style="fill: rgb(194, 194, 194); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 478.047px 298.847px;"
                                id="elq1knuxy58kd" class="animable"></path>
                        </g>
                        <g id="elt0v2saysn1n" class="animable" style="transform-origin: 424.385px 283.011px;">
                            <path
                                d="M443.90719,192.1474s-11.8357-1.6213-18.48319,6.3232c-6.64741,7.9445-7.94451,12.4843-5.99891,15.0784s2.5941,3.8912,2.5941,3.8912c0,0,3.7291,3.7291,2.9184,8.7552-.81059,5.0261-5.8368,9.2416-4.7018,10.3765,1.1349,1.135,4.7018,6.1611,13.7813,7.1339s24.15791-.3243,27.88691-2.7563c3.72909-2.432,6.32319-3.729,5.35039-5.5125s-2.5941-6.6475-2.5941-10.0523,3.5669-12.16,1.7835-18.8074c-1.7835-6.6475-7.1339-11.5115-13.4571-13.295-6.3232-1.7834-9.0795-1.1349-9.0795-1.1349Z"
                                style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 443.03px 218.021px;"
                                id="elwcamzxy2vbf" class="animable"></path>
                            <path
                                d="M414.80889,312.3257l-.4433,1.4408-1.2833,4.1706s-11.4387,7.5539-17.0501,14.4603c-5.6115,6.9064-11.6545,17.6976-11.6545,17.6976,0,0-12.5179,12.9495-22.8774,23.9565-10.3596,11.0071-13.597,14.0286-13.597,14.0286l-3.0215,.4317s.6474,13.5969,.6474,14.676,5.8273-4.7481,9.7121-7.1222c3.8849-2.3741,32.1579-14.8919,37.5535-20.7191,5.3956-5.8273,12.7337-12.5179,14.2444-14.2445,1.51081-1.7266,15.971-7.7696,15.971-7.7696l10.7913-5.8273h36.6901s4.964-4.1007,4.5323-13.1653c-.4316-9.0646-3.669-19.4242-3.669-19.4242l-56.546-2.5899Z"
                                style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 409.966px 357.823px;"
                                id="el165c1c5g92i" class="animable"></path>
                            <path d="M421.06779,322.4695s6.4748,4.1006,8.633,9.0646"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 425.384px 327.002px;"
                                id="eluael4ebcgpr" class="animable"></path>
                            <path
                                d="M346.17669,390.67s-1.0791-10.7912-3.2373-14.6761c-2.1583-3.8848-6.0431-2.8057-8.20139,.8633-2.15821,3.669-3.669,13.3812-1.94241,19.2084,1.7266,5.8273,6.2589,13.8128,8.4172,13.8128s4.7481-.4317,5.1797-4.7482c.4317-4.3164,.2159-6.2589,0-10.3595l-.2158-4.1007Z"
                                style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 339.344px 391.713px;"
                                id="el60runmkcxex" class="animable"></path>
                            <g id="elmiybo0338z" class="animable" style="transform-origin: 408.599px 367.597px;">
                                <path
                                    d="M453.87309,325.491s-11.6545,2.3741-17.9134,6.2589c-6.2589,3.8849-32.8053,21.3666-35.3952,24.1724-2.5899,2.8057-14.4144,17.1287-22.0141,24.1723-8.8488,8.2013-14.8919,11.2229-14.8919,11.2229,0,0-10.3596-8.633-13.1653-12.0862s-5.1798-1.5108-6.0431,1.7266,3.2943,12.1602,4.3165,14.4602c3.4532,7.7697,12.5178,14.8919,14.0286,14.2445,1.51081-.6475,14.2444-9.2805,14.2444-9.2805,0,0,9.9279-7.338,20.5033-11.8703,10.5754-4.5324,21.1509-17.6977,23.9566-20.7192s8.2013-5.8273,8.2013-5.8273c0,0,20.71921-6.4747,28.4888-10.1437,7.7697-3.669,12.5179-2.1583,14.6761-9.0646"
                                    style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 408.599px 367.597px;"
                                    id="el5hd9flqrr2f" class="animable"></path>
                                <path
                                    d="M371.58259,386.0148c-4.8954,3.7816-7.9241,5.3027-7.9241,5.3027,0,0-10.3596-8.633-13.1653-12.0862s-5.1798-1.5108-6.0431,1.7266c-.8633,3.2373,3.2943,12.1602,4.3165,14.4602,3.4532,7.7697,12.5178,14.8919,14.0286,14.2444,1.51081-.6474,14.2444-9.2804,14.2444-9.2804,0,0,.4565-.3371,1.2725-.9082l-6.7295-13.4591Z"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 361.322px 393.555px;"
                                    id="el3jpcsl0itxq" class="animable"></path>
                                <path
                                    d="M350.49319,379.2313c-2.8057-3.4532-5.1798-1.5108-6.0431,1.7266-.8633,3.2373,3.2943,12.1602,4.3165,14.4602,3.4532,7.7697,12.5178,14.8919,14.0286,14.2444,.6307-.2702,3.2174-1.9324,6.0164-3.7767l-9.2538-9.8202,4.1007-4.7481s-10.3596-8.633-13.1653-12.0862Z"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 356.572px 393.555px;"
                                    id="el8otyoe0k0ha" class="animable"></path>
                            </g>
                            <g id="el9vyg6tlcmtu" class="animable" style="transform-origin: 448.537px 238.364px;">
                                <path
                                    d="M435.63839,243.8679s-8.7552-5.0261-13.6192-10.8629-10.3765-14.592-13.457-20.1045c-3.0806-5.5126-17.024-33.7238-17.1861-34.6966-.1622-.9728,3.8911-8.1066,4.0533-9.0794,.1621-.9728,.4864-10.0523,.4864-11.6736,0-1.6214-.6486-1.4592-1.2971-.4864s-1.7835,6.9717-1.9456,7.6202c-.16209,.6486-2.9184-6.4853-3.8912-7.9445s-1.9456,.6485-1.297,1.7835c.6485,1.1349,1.9456,7.4581,1.9456,7.4581,0,0-5.0262-5.0261-5.5126-5.6747-.4864-.6485-1.297,.4864-.4864,1.9456,.8107,1.4592,3.4048,5.5126,3.4048,5.5126,0,0-4.2154-.8107-5.3504-1.4592-1.13489-.6486-1.7834,.8106-.1621,1.6213s4.5397,2.9184,4.5397,2.9184c0,0-2.7562,6.3232-1.9456,8.4309,.8107,2.1078,2.1078,3.7291,2.1078,3.7291,0,0,11.9978,40.5333,13.6191,44.2624,1.6214,3.7291,4.5398,8.1067,4.5398,8.1067l8.7827,19.886-.0275,53.8846-1.135,5.1883s15.2406,4.864,26.2656,5.8368c11.0251,.9728,19.7803,.4864,23.6715-.8107s11.02511-4.3776,11.02511-4.3776c0,0-3.08061-19.9424-4.70191-28.8597-1.6213-8.9174-.8107-17.024-.4864-17.5104,.32431-.4864,15.4027-29.6704,18.8075-40.8576,3.40481-11.1872,11.8357-41.6683,12.4842-42.8032,.64861-1.1349,5.3504-3.8912,5.3504-3.8912,0,0,7.7824-1.9456,8.431-3.7291,.6485-1.7834,4.3776-10.2144,4.0533-11.3493s-1.1349-.3243-1.7835,.3243c-.6485,.6485-1.6213,2.1077-1.6213,2.1077,0,0,1.2971-5.6747,1.1349-6.4853-.1621-.8107-1.1349,0-1.6213,.8106l-.4864,.8107s-.3243-2.432-1.4592-1.9456-2.1077,2.1077-2.432,2.5941c-.32429,.4864-3.5669,4.7019-3.5669,4.7019l-4.2155,1.7835-2.26989,.6485s3.08059-2.432,3.08059-3.8912-1.135-1.6213-1.94559-.8107c-.81071,.8107-.81071,.9728-3.24271,2.5942-2.432,1.6213-3.4048,5.8368-3.5669,7.7824-.1622,1.9456-2.1078,8.7552-2.1078,8.7552,0,0-11.9978,23.0229-13.457,26.752-1.4592,3.729-6.3232,14.592-6.3232,14.592,0,0-6.6475,8.7552-7.78239,9.8901-1.135,1.1349-5.02621,4.5397-5.67471,4.864-.6485,.3242-10.8629,1.4592-10.8629,1.4592l-10.863,.6485Z"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 448.537px 238.364px;"
                                    id="elb7jj3z09czk" class="animable"></path>
                                <path
                                    d="M392.02459,171.2322s-.6485-9.4037-1.94559-10.5387c-1.29711-1.1349-1.45921,1.135-1.45921,1.6214s.8107,5.5125,.8107,5.5125c0,0-2.7563,3.0805-2.5942,5.0261,.1622,1.9456,2.2699,4.0534,2.2699,4.0534"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 389.427px 168.643px;"
                                    id="eltl5lxucj1vo" class="animable"></path>
                                <path
                                    d="M497.89759,179.3389s2.7563-2.5942,4.5398-3.2427c1.7834-.6485,5.67461-1.2971,5.67461-1.2971,0,0,.32429-8.1066,1.135-8.1066,.81059,0,1.45919,1.9456,1.45919,3.0805s.6485,5.9989,.4864,6.8096c-.1622,.8107-.4864,1.7835-1.6214,2.1077"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 504.558px 173.016px;"
                                    id="el7b4ukfihurg" class="animable"></path>
                                <path
                                    d="M486.38609,227.6545c1.4415-4.7362,3.7836-12.9296,6.0548-20.8912l-10.3958-4.4554c-2.3233,4.5944-4.3532,8.7074-4.9005,10.1061-1.4592,3.7291-6.3232,14.592-6.3232,14.592,0,0-6.6475,8.7552-7.78239,9.8902-1.135,1.1349-5.02621,4.5397-5.67471,4.864-.6485,.3242-10.8629,1.4592-10.8629,1.4592l-10.8629,.6485s-8.7553-5.0261-13.6192-10.8629c-4.8641-5.8368-10.3766-14.592-13.4571-20.1045-.9667-1.7299-3.0034-5.6958-5.348-10.353l-10.02919,4.2548c2.89529,9.5437,5.72969,18.6866,6.45979,20.3659,1.6214,3.7291,4.5398,8.1066,4.5398,8.1066l8.7826,19.8861-.0274,53.8846-1.135,5.1882s15.2406,4.8641,26.2657,5.8369c11.025,.9728,19.7802,.4864,23.6714-.8107s11.02511-4.3776,11.02511-4.3776c0,0-3.08061-19.9424-4.70191-28.8597-1.6214-8.9174-.8107-17.024-.4864-17.5104,.3242-.4864,15.4026-29.6704,18.8074-40.8577Z"
                                    style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 442.813px 261.446px;"
                                    id="elnvp7ayn24dk" class="animable"></path>
                                <path
                                    d="M434.01709,235.2749s-.8107,6.161-1.1349,8.2687c-.3239,2.1053-.6478,3.0783-.6489,3.0819-.0681,.2338,16.4929,3.3347,16.5379-.0013l.1622-11.9979s-10.5387,2.1078-14.9163,.6486Z"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 440.583px 241.393px;"
                                    id="elcuprikanzib" class="animable"></path>
                                <path d="M427.23119,244.0854s-3.0206,7.2159-4.1953,10.4043"
                                    style="fill: none; stroke: rgb(255, 255, 255); stroke-linecap: round; stroke-linejoin: round; transform-origin: 425.134px 249.288px;"
                                    id="el34hf3yu9dux" class="animable"></path>
                                <path d="M425.38529,241.9038s-5.2022,7.3837-5.8734,8.5584"
                                    style="fill: none; stroke: rgb(255, 255, 255); stroke-linecap: round; stroke-linejoin: round; transform-origin: 422.449px 246.183px;"
                                    id="elhvjqtsz0grb" class="animable"></path>
                                <path d="M455.42349,244.421s1.0069,7.7193,1.5104,9.0618"
                                    style="fill: none; stroke: rgb(255, 255, 255); stroke-linecap: round; stroke-linejoin: round; transform-origin: 456.179px 248.952px;"
                                    id="el2h9ldl9u6qr" class="animable"></path>
                            </g>
                            <g id="ellpxurxd7rfl" class="animable" style="transform-origin: 442.662px 220.359px;">
                                <path
                                    d="M443.42079,201.5511s.6486,5.1569,3.7291,8.4937,8.59311,6.8253,8.59311,6.8253l.16209,5.6119s2.1077-1.9717,2.9184-.455,.4864,4.2468-1.1349,5.7636c-1.6214,1.5167-3.4048,.91-3.4048,.91,0,0-1.7285,3.8394-3.0806,5.1569-3.89119,3.7918-8.4309,5.3085-11.5114,5.3085s-6.6475-2.275-8.9174-5.9152c-2.2698-3.6402-3.4048-6.6736-3.4048-9.2521s-1.297-12.5888-1.297-12.5888c0,0,14.1056-4.8536,17.3482-9.8588Z"
                                    style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 442.662px 220.359px;"
                                    id="elemhnyybte2c" class="animable"></path>
                                <path
                                    d="M439.36749,220.0343s-1.4592,.6486-.3243,1.9456c1.135,1.2971,2.432,.1622,2.432,.1622"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 440.043px 221.318px;"
                                    id="elby9mgsf9wpn" class="animable"></path>
                                <path
                                    d="M434.17919,226.3575s6.6475,4.2155,11.83581,.4864c0,0-3.08061,4.3776-6.64751,4.3776-2.2699,0-5.1883-4.864-5.1883-4.864Z"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 440.097px 228.789px;"
                                    id="el9ebh0okxeqa" class="animable"></path>
                                <path d="M445.04219,218.413s1.9456-2.432,5.1882,.6485"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 447.636px 218.281px;"
                                    id="els4go3ygnpj" class="animable"></path>
                                <path d="M436.12479,218.2509s-3.40479-2.7563-5.3504,.1621"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 433.45px 217.743px;"
                                    id="eli0864vxtxb" class="animable"></path>
                                <path d="M437.74619,212.5762s-4.0534-1.2971-7.1339,2.2699"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 434.179px 213.596px;"
                                    id="els728gggfr6d" class="animable"></path>
                                <path d="M443.74509,212.5762s5.99891-.6485,6.8096,3.0805"
                                    style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 447.15px 214.095px;"
                                    id="el7ihxoyhrzhq" class="animable"></path>
                            </g>
                        </g>
                        <g id="elmen5kj7lihi" class="animable" style="transform-origin: 392.189px 307.024px;">
                            <rect x="350.10349" y="304.9885" width="84.171" height="4.0712"
                                style="fill: rgb(194, 194, 194); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 392.189px 307.024px;"
                                id="elmp6ep8oovb" class="animable"></rect>
                            <rect x="398.22959" y="304.9885" width="36.0448" height="4.0712"
                                style="fill: rgb(133, 133, 133); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 416.252px 307.024px;"
                                id="elp664a8ipwnm" class="animable"></rect>
                        </g>
                    </g>
                </g>
                <g id="freepik--Character_1--inject-25" class="animable"
                    style="transform-origin: 115.928px 293.289px;">
                    <g id="elf7usprgwg2m" class="animable" style="transform-origin: 91.806px 360.439px;">
                        <g id="elpxdzfuyw34" class="animable" style="transform-origin: 108.475px 360.439px;">
                            <polyline points="121.31422 315.6972 118.90291 353.6069 121.66014 437.5162"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 120.282px 376.607px;"
                                id="el1vmd0hfdsxp" class="animable"></polyline>
                            <polyline points="161.6699 315.6972 159.2586 353.6069 162.0158 437.5162"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 160.637px 376.607px;"
                                id="el6tl7hllx026" class="animable"></polyline>
                            <polyline points="159.2586 353.6069 107.8741 353.6069 97.3465 437.0149"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 128.303px 395.311px;"
                                id="ela0ni9tg8y2n" class="animable"></polyline>
                            <polyline points="111.8845 353.6069 60.5 353.6069 49.9724 437.0149"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 80.9284px 395.311px;"
                                id="el7rvejqby454" class="animable"></polyline>
                            <rect x="58.7454" y="349.5358" width="100.5132" height="4.0712"
                                style="fill: rgb(194, 194, 194); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 109.002px 351.571px;"
                                id="elu2zvryallf" class="animable"></rect>
                            <rect x="107.8741" y="349.5358" width="51.3845" height="4.0711"
                                style="fill: rgb(133, 133, 133); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 133.566px 351.571px;"
                                id="el5zwlcn877p8" class="animable"></rect>
                            <path
                                d="M162.2201,315.6972h-42.45789c-1.76753,0-3.12663-1.3893-3.03564-3.1032l1.38724-26.1283c.09099-1.7139,1.59762-3.1032,3.36514-3.1032h42.45795c1.7675,0,3.1266,1.3893,3.0356,3.1032l-1.3872,26.1283c-.091,1.7139-1.5976,3.1032-3.3652,3.1032Z"
                                style="fill: rgb(194, 194, 194); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 141.85px 299.53px;"
                                id="elvnfy6h87l2" class="animable"></path>
                        </g>
                        <path d="M51.2257,307.9268v38.2019c0,3.9582,3.2087,7.1669,7.1669,7.1669h7.8725"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; stroke-width: 3px; transform-origin: 58.7454px 330.611px;"
                            id="elr1zbpe2336t" class="animable"></path>
                        <g id="elwoy44urgr5e" class="animable" style="transform-origin: 58.7205px 307.706px;">
                            <rect x="16.6351" y="305.6709" width="84.1709" height="4.0712"
                                style="fill: rgb(194, 194, 194); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 58.7206px 307.707px;"
                                id="el33mi0i2kswh" class="animable"></rect>
                            <rect x="64.7612" y="305.6709" width="36.0448" height="4.0712"
                                style="fill: rgb(133, 133, 133); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 82.7836px 307.707px;"
                                id="elnq3u50ynxd" class="animable"></rect>
                        </g>
                    </g>
                    <g id="elejg7b8ftww" class="animable" style="transform-origin: 139.548px 293.289px;">
                        <path
                            d="M87.6947,278.2013s-.8644,39.7604-.8644,45.8109,3.6735,37.8157,3.6735,37.8157l-18.3676,47.3235-3.4574,7.7792s1.9448,2.5931,4.97,3.0253c3.0253,.4322,4.9701,.6482,4.9701,.6482,0,0,34.7904-54.0223,35.0065-56.6153,.2161-2.5931,4.10566-49.2684,4.10566-49.2684l2.59307,2.1609,19.44807,56.1832s4.97,38.4639,4.97,39.7604v13.3976s9.0758,1.0804,10.3723,.8643,3.4574-39.5443,3.6735-41.273,.4322-22.6894,.4322-23.9859c0-1.2966-7.347-44.2983-8.4275-50.3488-1.0804-6.0505-2.593-16.6389-2.593-16.6389l-1.0805-7.9953s-20.31239,3.6735-27.01115,1.7287c-6.69875-1.9448-32.41335-10.3723-32.41335-10.3723Z"
                            style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 113.95px 352.658px;"
                            id="el38l06kvhs6l" class="animable"></path>
                        <path
                            d="M155.1145,427.0868l5.6183,3.8896s13.6137,4.3218,13.6137,5.4023c0,1.0804-2.1609,1.7287-3.2414,1.9448-1.0804,.2161-26.1468,0-26.1468,0,0,0-.8643-1.2966-.8643-3.0253s.6482-9.0757,.6482-9.0757l10.3723,.8643Z"
                            style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 159.22px 432.321px;"
                            id="el0w4onomyogcc" class="animable"></path>
                        <path
                            d="M68.6788,416.9306l-2.5931,6.6988s-2.593,7.1309-2.1609,11.2366c.4322,4.1057,2.1609,6.0505,4.9701,4.5379,2.8091-1.5126,6.0505-4.754,7.347-8.8596,1.2966-4.1057,2.377-9.9402,2.377-9.9402,0,0-9.0757-1.2965-9.9401-3.6735Z"
                            style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 71.2476px 428.423px;"
                            id="elc8s65qeubo" class="animable"></path>
                        <path
                            d="M195.057,186.7739s-11.1763-9.3801-12.5733-9.9788c-1.3971-.5987-7.9831,6.586-8.3823,7.3843-.3991,.7983,9.5797,6.1869,10.378,6.3865,.7984,.1995,.5987,.3991,2.1954,.3991,1.5966,0,2.794-.3991,2.794-.3991l3.1933,3.1931,.1114-.0581,5.2771,21.812s-22.5521-5.9873-25.1466-7.7835c-2.5945-1.7961-7.3843-3.7919-14.3695-8.1826s-18.361-10.9767-29.93647-10.5776c0,0-21.95343,5.9873-30.93433,10.378s-13.3716,9.5797-14.5691,12.1742c-1.1974,2.5945-10.9767,24.1487-12.1741,27.7411-1.1975,3.5924,3.5923,8.5818,7.1847,9.1805,3.5924,.5988,6.3865-1.397,6.3865-1.397l13.3715-17.7623s-7.983,32.3313-9.38,38.3186c-1.3971,5.9873-1.9958,11.1763-.1996,12.7729s26.1445,11.9746,36.32292,11.9746,21.75378-1.1974,22.95128-3.3928c1.1975-2.1953,1.397-19.3589,1.5966-20.1572s10.378-38.1191,10.9767-38.9174,42.3102,2.3949,46.5013,1.397c4.1911-.9978,8.1826-5.1889,8.5818-8.5817s-20.1572-35.9238-20.1572-35.9238Z"
                            style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 142.976px 234.555px;"
                            id="elzxgw8jneqji" class="animable"></path>
                        <path d="M120.82215,278.9119c-1.07539,.377-2.09644,.7073-3.03902,.9766"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 119.303px 279.4px;"
                            id="elpvkvxhia0mi" class="animable"></path>
                        <path d="M145.5079,267.8744s-12.24009,6.277-21.97692,10.0367"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 134.519px 272.893px;"
                            id="elzpoq9qwt1ag" class="animable"></path>
                        <path d="M126.56265,273.1884s-14.09345,6.4691-20.10045,7.3932"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 116.512px 276.885px;"
                            id="el3lbqwu7oua6" class="animable"></path>
                        <polyline points="102.0724 222.5906 97.8632 229.2837 106.4622 222.8216"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 102.163px 225.937px;"
                            id="elr1tcojkydd" class="animable"></polyline>
                        <path
                            d="M195.057,186.7739s-11.1763-9.3801-12.5733-9.9788c-1.3971-.5987-7.9831,6.586-8.3823,7.3843-.3991,.7983,9.5797,6.1869,10.378,6.3865,.7984,.1995,.5987,.3991,2.1954,.3991,1.5966,0,2.794-.3991,2.794-.3991l3.1933,3.1931,.1114-.0581,2.1344,8.8219c2.3485,1.3672,6.2955,2.9026,10.6571,1.4126-5.179-8.727-10.508-17.1615-10.508-17.1615Z"
                            style="fill: rgb(194, 194, 194); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 189.827px 190.653px;"
                            id="elbie61c2wm" class="animable"></path>
                        <line x1="176.4964" y1="185.9756" x2="182.0845" y2="180.3875"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 179.29px 183.182px;"
                            id="elntkysox4v1" class="animable"></line>
                        <line x1="179.49" y1="187.7718" x2="184.4794" y2="182.5828"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 181.985px 185.177px;"
                            id="ely2q1y1aagv" class="animable"></line>
                        <line x1="183.0824" y1="189.7676" x2="186.8743" y2="184.5786"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 184.978px 187.173px;"
                            id="el7xjxogt6pq4" class="animable"></line>
                        <polyline points="186.6748 190.965 189.2693 187.3726 187.2735 187.5722"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 187.972px 189.169px;"
                            id="eld5jrx32f8x" class="animable"></polyline>
                        <path
                            d="M174.1014,184.1794s-3.1932,2.3949-2.3949,3.1932,3.1933,1.9958,4.5903,2.5945c1.397,.5988,1.7962-2.5945,1.7962-2.5945l-3.9916-3.1932Z"
                            style="fill: rgb(194, 194, 194); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 174.836px 187.111px;"
                            id="elaiu5ztn2nok" class="animable"></path>
                        <path
                            d="M128.79762,183.3811s-2.39492,10.7771-2.39492,13.9704,1.59661,12.5733,9.1805,13.7707c7.5839,1.1975,11.775-10.7771,11.775-10.7771l-16.7644-19.3589-1.79618,2.3949Z"
                            style="fill: rgb(194, 194, 194); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 136.88px 196.096px;"
                            id="eljj8vh89yhpi" class="animable"></path>
                        <g id="el7obvb7yjvh" class="animable" style="transform-origin: 144.066px 174.896px;">
                            <path
                                d="M135.7828,147.8565s-5.58815,2.3949-7.18476,6.3865c-1.59661,3.9915-.79831,18.5606-.79831,18.5606,0,0-3.99152-3.1933-4.59025,2.794s2.39491,8.5818,3.59237,8.981c1.19746,.3991,2.5945,.5987,2.5945,.5987,0,0,1.59661,5.3886,3.39279,7.3843,1.79619,1.9958,8.38226,9.3801,11.77506,10.378s7.3843-2.1953,8.9809-3.9915,6.7856-10.5776,7.5839-13.5712c.7983-2.9937,.7983-6.586,1.1975-8.981,.3991-2.3949,4.1911-8.9809,1.9957-15.9661-2.1953-6.9851-6.1869-10.9767-13.7708-12.9724-7.5839-1.9958-14.7686,.3991-14.7686,.3991Z"
                                style="fill: rgb(194, 194, 194); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 144.066px 174.896px;"
                                id="elah9vgr977v5" class="animable"></path>
                            <path
                                d="M164.3222,160.4298c-2.1953-6.9851-6.1868-10.9767-13.7707-12.9724-7.584-1.9958-14.7687,.3991-14.7687,.3991,0,0-5.58812,2.3949-7.18477,6.3864-1.59659,3.9916-.79827,18.5607-.79827,18.5607,0,0,2.39492,1.9957,2.99362,1.397,.59876-.5987-.79833-1.9958,1.39702-3.1933,2.19536-1.1974,4.3907,.3992,4.7898-2.794,.3992-3.1933,2.395-10.1784,5.5882-10.1784s7.9831,4.3907,11.1763,4.5902c3.1932,.1997,6.1868,0,7.7835-.9978,1.5966-.9979,2.794-1.1975,2.794-1.1975Z"
                                style="fill: rgb(38, 50, 56); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 145.961px 160.488px;"
                                id="ellvuqeq97zl" class="animable"></path>
                            <path
                                d="M147.1358,181.1379c-.2083,1.1385-.9925,1.9487-1.7515,1.8098-.7589-.1389-1.2053-1.1744-.9969-2.3128s.9925-1.9487,1.7515-1.8098c.7589,.1389,1.2053,1.1744,.9969,2.3128Z"
                                style="fill: rgb(38, 50, 56); transform-origin: 145.762px 180.886px;" id="elme2sxem5xvi"
                                class="animable"></path>
                            <path
                                d="M159.8868,181.1722c-.2083,1.1384-.9925,1.9487-1.7515,1.8098-.7589-.1389-1.2053-1.1744-.9969-2.3129,.2084-1.1384,.9925-1.9487,1.7515-1.8098,.7589,.1389,1.2053,1.1744,.9969,2.3129Z"
                                style="fill: rgb(38, 50, 56); transform-origin: 158.513px 180.921px;" id="eluay2fkv4aul"
                                class="animable"></path>
                            <path
                                d="M156.9379,177.3938s-2.5945,3.3928-2.7941,4.5903c-.1996,1.1974-.1996,2.7941-.1996,2.7941"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 155.441px 181.086px;"
                                id="elzs2j5g2mfyl" class="animable"></path>
                            <path d="M149.9527,176.3959s-3.9915-3.5923-7.5839-.9978"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 146.161px 175.426px;"
                                id="eltm84j1hqyxm" class="animable"></path>
                            <path d="M158.5345,176.9947s2.3949-2.5945,3.792-.5988"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 160.431px 176.318px;"
                                id="elv6sg03gjme" class="animable"></path>
                            <path d="M153.7447,189.1688s-.7983,.9979-1.9958,1.1975-4.7898-.5987-4.7898-1.5966"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 150.352px 189.583px;"
                                id="elpta5avja4b8" class="animable"></path>
                            <path
                                d="M152.1481,193.7591c-6.7856,1.5966-10.1784-3.5924-10.1784-3.5924,0,0,.9978,6.1869,2.9936,6.586,1.9958,.3992,7.1848-2.9936,7.1848-2.9936Z"
                                style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 147.059px 193.476px;"
                                id="elqleuufu4kla" class="animable"></path>
                        </g>
                        <path d="M198.0506,215.5129s1.1975,9.1805,3.5924,10.7771"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 199.847px 220.901px;"
                            id="el840ihhb2pur" class="animable"></path>
                        <path d="M196.0549,218.5066s.5987,4.5902,1.5966,6.586"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 196.853px 221.8px;"
                            id="elnghkn0h4i" class="animable"></path>
                        <polyline points="162.9252 215.7125 160.1311 229.8824 166.5176 217.5087"
                            style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 163.324px 222.797px;"
                            id="eltf2wy73z9" class="animable"></polyline>
                        <g id="elt4f48zjpgqe" class="animable" style="transform-origin: 91.4051px 217.568px;">
                            <path
                                d="M100.6573,183.1815l-3.3928,20.756s-20.3567,22.7517-24.7474,29.9364c-4.3907,7.1848-5.7877,11.7751-2.5945,14.9683s7.3843,4.5902,9.9788,3.1932,24.5479-46.7009,24.5479-46.7009l2.3949-2.5945,2.9937-5.3885s.3991,.9978,1.397,.3991,3.3928-5.9873,3.3928-6.9852c0-.9978-4.9894-5.1889-6.586-5.9872-1.5966-.7984-5.9873-3.3928-7.3844-1.5967Z"
                                style="fill: rgb(194, 194, 194); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 91.4051px 217.568px;"
                                id="elw856et0ztf" class="animable"></path>
                            <path
                                d="M83.4601,219.8345c-4.6624,5.5361-9.1436,11.0949-10.943,14.0394-4.3907,7.1848-5.7877,11.775-2.5945,14.9683,3.1932,3.1932,7.3843,4.5902,9.9788,3.1932,1.317-.7092,7.6216-12.731,13.5114-24.3984-4.528-1.8928-7.86-5.1941-9.9527-7.8025Z"
                                style="fill: #92E3A9; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 80.7976px 236.205px;"
                                id="el8rwcdg6idm" class="animable"></path>
                            <path d="M105.6864,193.4371s1.2466-3.3564,1.6302-3.4523,7.0234,.6847,7.3111,.7806"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 110.157px 191.707px;"
                                id="elhrhtaokyo7" class="animable"></path>
                            <path d="M104.3438,187.8751s1.3426-1.822,2.2057-1.6302c.863,.1918,5.466,1.7261,5.466,1.7261"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 108.18px 187.101px;"
                                id="elwl1peo5q6ab" class="animable"></path>
                            <path d="M103.0972,184.5188s0-.6713,.6713-.6713c.6712,0,4.2732,.9307,4.2732,.9307"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 105.569px 184.313px;"
                                id="el5pznu9sala" class="animable"></path>
                            <polyline points="109.8379 197.3515 111.0566 194.5878 112.3991 194.4919"
                                style="fill: none; stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 111.118px 195.922px;"
                                id="elhyijrn01gv5" class="animable"></polyline>
                            <path
                                d="M106.2618,183.5598s2.9727-.5754,3.644-.1918,3.0687,1.6302,3.4523,2.1097,.0959,1.0549-.5754,1.2467c-.6713,.1917-1.7261,0-1.7261,0l-4.7948-3.1646Z"
                                style="fill: rgb(194, 194, 194); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 109.896px 185.023px;"
                                id="elcrxx4ddzrud" class="animable"></path>
                        </g>
                    </g>
                </g>
                <g id="freepik--Papers--inject-25" class="animable" style="transform-origin: 395.175px 173.925px;">
                    <g id="el75n1rpuytmr" class="animable" style="transform-origin: 467.488px 140.368px;">
                        <path
                            d="M451.59139,123.6685s-3.6107,10.8321-4.51339,18.7305c-.90271,7.8984,.90269,14.6685,.90269,14.6685,0,0,20.3102-2.9337,23.4696-4.062,3.1594-1.1284,11.0578-3.1594,11.0578-3.1594,0,0-3.6107-2.4824-.9027-10.8321,2.708-8.3498,6.5444-12.1862,6.5444-12.1862,0,0-29.5627,2.4824-36.5584-3.1593Z"
                            style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 467.488px 140.368px;"
                            id="elgfoh8npfde4" class="animable"></path>
                        <path
                            d="M468.78749,153.4795s3.7077-1.5549,5.3821-3.9469c1.6745-2.3921,2.6313-5.1429,2.6313-5.1429,0,0,4.4774,3.3779,5.7072,5.4564,0,0-6.5444,3.2746-13.7206,3.6334Z"
                            style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 475.648px 148.935px;"
                            id="elvjn3q9s7zn" class="animable"></path>
                    </g>
                    <path
                        d="M382.47589,107.7915s0,4.8371-3.0231,13.3019c-3.0232,8.4648-9.6741,20.5574-9.6741,20.5574l27.20831,15.7203s11.40889-17.7319,11.488-35.6731c.03769-8.5507,1.20919-12.6972,1.20919-12.6972,0,0-18.7435,2.4185-27.2083-1.2093Z"
                        style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 389.731px 132.581px;"
                        id="elin02u2yudtj" class="animable"></path>
                    <path
                        d="M604.34989,130.8425s-3.6416-3.1837-8.0245-11.0312c-4.3829-7.8474-9.1093-20.8138-9.1093-20.8138l-29.7433,10.1368s5.84021,20.2602,19.2953,32.1285c6.4125,5.6563,8.7631,9.2675,8.7631,9.2675,0,0,10.5161-15.7029,18.8187-19.6878Z"
                        style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 580.911px 124.764px;"
                        id="elhqddy3kcfav" class="animable"></path>
                    <path
                        d="M296.014,106.5823s-16.325,7.8602-25.9991,13.9065c-9.674,6.0462-12.6972,8.4648-12.6972,8.4648l17.5343,25.999s16.325-10.8833,19.9527-13.9065c3.6278-3.0231,19.3482-13.9064,19.3482-13.9064,0,0-10.2787-13.9065-18.1389-20.5574Z"
                        style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 285.735px 130.767px;"
                        id="elxi2tz4eger7" class="animable"></path>
                    <path
                        d="M452.57239,56.5969s-16.325,7.8602-25.999,13.9065c-9.6741,6.0463-12.6972,8.4648-12.6972,8.4648l17.5342,25.999s16.325-10.8833,19.9528-13.9064c3.6277-3.0232,19.3481-13.9065,19.3481-13.9065,0,0-10.2787-13.9065-18.1389-20.5574Z"
                        style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 442.294px 80.782px;"
                        id="elwd3gcgfwxe" class="animable"></path>
                    <g id="elyrs4d35dn3" class="animable" style="transform-origin: 205.647px 274.212px;">
                        <path
                            d="M199.9114,257.4157s-2.1966,3.661-5.3694,7.322-5.8575,4.3931-5.8575,4.3931c0,0,9.7625,7.0779,11.9591,10.2508,2.1966,3.1728,8.7864,11.7151,8.7864,11.7151,0,0,3.6609,.7322,6.8338-.9763,3.1728-1.7084,9.0304-13.4235,9.0304-13.4235,0,0-20.9896-14.6439-25.3828-19.2812Z"
                            style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 206.989px 274.334px;"
                            id="elqww975jstus" class="animable"></path>
                        <path
                            d="M188.6845,269.1308s-2.6848-3.6609-2.6848-4.8812c0-1.2204,1.9526-7.0779,1.9526-7.0779,0,0,6.5897,10.2507,10.7388,15.3761,4.1491,5.1253,16.8405,13.6676,16.8405,13.6676,0,0-5.1254,1.4644-6.1016,4.8813,0,0-8.5423-8.2982-12.9355-12.4473-4.3931-4.1491-6.1016-6.5898-7.81-9.5186Z"
                            style="fill: rgb(255, 255, 255); stroke: rgb(38, 50, 56); stroke-linecap: round; stroke-linejoin: round; transform-origin: 200.766px 274.134px;"
                            id="eltykv42nybic" class="animable"></path>
                    </g>
                </g>
                <defs>
                    <filter id="active" height="200%">
                        <feMorphology in="SourceAlpha" result="DILATED" operator="dilate" radius="2">
                        </feMorphology>
                        <feFlood flood-color="#32DFEC" flood-opacity="1" result="PINK"></feFlood>
                        <feComposite in="PINK" in2="DILATED" operator="in" result="OUTLINE"></feComposite>
                        <feMerge>
                            <feMergeNode in="OUTLINE"></feMergeNode>
                            <feMergeNode in="SourceGraphic"></feMergeNode>
                        </feMerge>
                    </filter>
                    <filter id="hover" height="200%">
                        <feMorphology in="SourceAlpha" result="DILATED" operator="dilate" radius="2">
                        </feMorphology>
                        <feFlood flood-color="#ff0000" flood-opacity="0.5" result="PINK"></feFlood>
                        <feComposite in="PINK" in2="DILATED" operator="in" result="OUTLINE"></feComposite>
                        <feMerge>
                            <feMergeNode in="OUTLINE"></feMergeNode>
                            <feMergeNode in="SourceGraphic"></feMergeNode>
                        </feMerge>
                        <feColorMatrix type="matrix"
                            values="0   0   0   0   0                0   1   0   0   0                0   0   0   0   0                0   0   0   1   0 ">
                        </feColorMatrix>
                    </filter>
                </defs>
            </svg>
        </div>

    </div>
    <div class="container" style="padding: 50px; display: flex;">
        <div class="col-md-12">
            <div id="contenu" class="contenu"> 
  <div>
<div id="block-pagetitle" class="titre-publications">



<h1>
<span>
   Les résultats de premier devoire</span>
</h1>


</div>
<div id="block-montheme-content">


<article data-history-node-id="219" role="article" about="/node/219" class="single-publication">





<div>

<div>  <img src="{{ asset('img/RESULTATS_4.jpg') }}" width="300" height="200" alt="Résultats Annuels " loading="lazy" typeof="foaf:Image">


</div>

<div><p><strong>Veuillez télécharger les résultats.</strong></p></div>

<div class="card">
      <a href="files/resultats/resultats_1em_devoire_(1AS).pdf" download>resultats_1em_devoire_(1AS).pdf</a>
      <a href="files/resultats/resultats_1em_devoire_(2AS).pdf" download>resultats_1em_devoire_(2AS).pdf</a>
      <a href="files/resultats/resultats_1em_devoire_(3AS).pdf" download>resultats_1em_devoire_(3AS).pdf</a>
      <a href="files/resultats/resultats_1em_devoire_(4AS).pdf" download>resultats_1em_devoire_(4AS).pdf</a>
      <a href="files/resultats/resultats_1em_devoire_(5AS).pdf" download>resultats_1em_devoire_(5AS).pdf</a>
      <a href="files/resultats/resultats_1em_devoire_(6AS).pdf" download>resultats_1em_devoire_(6AS).pdf</a>
  </div>

<div style="display: none;">single-publication</div>

</div>

</article>

</div>

</div>

</div>
      </div>
    </div>
    <footer>
        <p class="text-center">&copy; 2023 École. Tous droits réservés.</p>
    </footer>



    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js"></script>

@endsection
