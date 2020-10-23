<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="shortcut icon" href="{{ mix('img/refresh-cw.svg') }}" type="image/svg">

    <link rel="icon" type="image/svg+xml" href="{{ mix('img/refresh-cw.svg') }}">
    <link rel="alternate icon" href="/favicon.ico">

    {{-- Site title and description --}}
    <title>{{ config('app.name') }}</title>
    <meta name="description" content="CI/CD pipeline with Lasso and GitHub actions.">

    {{-- Stylesheets --}}
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>

<body class="antialiased bg-gray-800 p-6 lg:px-0">
    <div class="min-h-screen flex flex-col">
        <div class="max-w-4xl mx-auto bg-white py-8 px-4 lg:px-6">
            <div x-data="{ show: false }" class="sticky top-0 bg-white py-4 border-b border-gray-500 space-y-4 z-10 sm:py-6 sm:space-y-6 lg:py-8 lg:space-y-8">
                <div class="flex justify-between items-center">
                    <div class="inline-flex shadow-lg hover:shadow-2xl transition ease-in-out duration-300">
                        <a href="https://github.com/alexjustesen/lasso-ci-cd" class="flex items-center px-4 py-2 border border-gray-600 text-base font-medium text-gray-700 bg-white hover:bg-gray-700 hover:border-gray-800 hover:text-white focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-300 lg:text-lg" rel="noopener">
                            <x-feathericon-github class="-mx-0.5 h-5 w-5" />
                            <span class="hidden lg:inline-block lg:ml-2">TL;DR</span>
                        </a>
                    </div>

                    <div class="inline-flex shadow-lg hover:shadow-2xl transition ease-in-out duration-300">
                        <button @click="show = ! show" class="flex items-center px-4 py-2 border border-gray-600 text-base font-medium text-gray-700 bg-white hover:bg-gray-700 hover:border-gray-800 hover:text-white focus:outline-none focus:shadow-outline-gray transition ease-in-out duration-300 lg:text-lg" rel="noopener">
                            <x-feathericon-menu class="-mx-0.5 h-5 w-5" />
                            <span class="hidden lg:inline-block lg:ml-2">Menu</span>
                        </button>
                    </div>
                </div>

                <article x-cloak x-show="show" class="prose max-w-none lg:prose-xl">
                    <h4>Table of contents</h4>
                    <ul>
                        <li><a @click="show = false" href="#getting-started">Getting Started</a></li>
                        <li><a @click="show = false" href="#configuring">Configuring</a></li>
                        <li><a @click="show = false" href="#github-actions">GitHub Actions</a></li>
                        <li><a @click="show = false" href="#deployment">Deployment</a></li>
                        <li><a @click="show = false" href="#disclaimer">Disclaimer</a></li>
                        <li><a @click="show = false" href="#about-me">About Me</a></li>
                    </ul>
                </article>
            </div>

            <article class="mt-4 prose max-w-none lg:prose-xl sm:mt-6 lg:mt-8">
                <h1>CI/CD Pipeline with GitHub Actions and Lasso</h1>

                <p>
                    Let's go over how I use GitHub Actions with <a href="https://getlasso.dev" target="_blank" rel="noopener">Lasso</a> from <a href="https://github.com/Sammyjo20" target="_blank" rel="noopener">Sam Carré</a>
                    to take compiled Webpack assets out of Github and upload them to any S3 compliant cloud storage provider.
                </p>

                <blockquote>
                    <p><strong>Warning</strong>: This article is very much a work-in-progress and in need of polishing my brain dump.</p>
                </blockquote>

                <h2 id="getting-started">Getting Started</h2>

                <h3>What is CI/CD?</h3>
                <p>
                    "CI/CD" is Continuous (Integeration or Iteration) / Continuous (Development, Delivery or Deployment) is a process of developing, testing and deploying your code.
                    You'll also likely see this referred to as an "Agile Development Cycle".
                </p>

                <p>
                    I recently answered a <a href="https://www.reddit.com/r/laravel/comments/j9fsa4/how_to_manage_and_maintain_laravel_application/g8kzlv1?utm_source=share&utm_medium=web2x&context=3" target="_blank" rel="noopener">Reddit</a> thread
                    which sparked this whole thing. If you're still unsure what CI/CD is I suggest doing some <a href="https://googlethatforyou.com?q=what%20is%20ci%2Fcid" target="_blank" rel="noopener">Googling</a>.
                </p>

                <h3>Assumptions</h3>
                <p>
                    Let's all get on the same page so we don't make a 🍑 out of you and me.
                </p>

                <ul>
                    <li>You're using GitHub as your source code management (SCM) platform and/or want to use GitHub Actions.</li>
                    <li>
                        You're using <a href="https://laravel.com" target="_blank" rel="noopener">Laravel</a> and that you're familare with Laravel's file system and in specific using
                        the <a href="https://laravel.com/docs/8.x/filesystem#composer-packages" target="_blank" rel="noopener">S3 driver</a>.
                    </li>
                    <li>
                        You compile your assets using <a href="https://laravel.com/docs/8.x/mix#introduction" target="_blank" rel="noopener">Laravel Mix</a> and they get published into the default <code>/public</code> directory.
                        <ul>
                            <li>For this example I'm using <a href="https://tailwind.css" target="_blank" rel="noopener">Tailwind CSS</a> and <a href="https://github.com/alpinejs/alpine/" target="_blank" rel="noopener">Alpine.js</a>.</li>
                        </ul>
                    </li>
                </ul>

                <h3 id="branching">Branching</h3>
                <p>
                    For this example we're going to use a pretty simple <span class="line-through">master</span> main, develop and feature branching model. Develop will be our default branch where all code is based from and main will be our production code.
                </p>

                <img src="https://wac-cdn.atlassian.com/dam/jcr:a9cea7b7-23c3-41a7-a4e0-affa053d9ea7/04%20(1).svg?cdnVersion=1315" alt="">

                <blockquote>
                    <p>Image source: <a href="https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow" target="_blank" rel="noopener">https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow</a></p>
                </blockquote>

                <h3>Process</h3>
                <p>
                    We're going to go into some detail but the overall summary of the CI/CD pipeline looks like this.
                </p>

                <ol>
                    <li>Create a new branch off <code>develop</code> to do your work in.</li>
                    <li>Create a pull request to bring those changes into the develop branch.</li>
                    <li>On the PR to <code>develop</code> run our tests and only allow a merge to occur if tests pass.</li>
                    <li>When successfully merged into <code>develop</code> run the tests again, compile and upload the assets using Lasso and deploy the code.
                        <ul>
                            <li>As a bonus we're doing to deploy code in both our <code>main</code> and <code>develop</code> branches.</li>
                        </ul>
                    </li>
                    <li>Once you have enough code in <code>develop</code> for a release open a PR our <code>main</code> branch.</li>
                    <li>When successfully merged into <code>main</code> run the tests again, compile and upload the assets using Lasso and deploy the code.</li>
                </ol>

                <h2 id="configuring">Configuring</h2>

                <h3>Installing dependencies</h3>
                <p>
                    First we need to require a couple of packages.
                </p>

                <ul>
                    <li>Require Amazon S3 <code>league/flysystem-aws-s3-v3</code>.</li>
                    <li>Require Lasso <code>composer require sammyjo20/lasso</code>.</li>
                </ul>

                <h3>Configuring Laravel</h3>
                <p>
                    Next we need to update Laravel's <code>.env</code> files with additional S3 driver options and the Lasso environment.
                </p>

                <ul>
                    <li>Add <code>AWS_ENDPOINT=</code> after <code>AWS_BUCKET</code> in both the <code>.env</code> and <code>.env-example</code>.</li>
                    <li>Add <code>LASSO_ENV=</code> to <code>.env</code> and <code>.env-example</code>.</li>
                </ul>

                <h3>Configuring Lasso</h3>
                <p>
                    Next let's get Lasso configured, for a full explaination of the settings go to the <a href="https://github.com/Sammyjo20/Lasso#configuration" target="_blank" rel="noopener">readme</a>.
                </p>

                <ul>
                    <li>Publish Lasso's config file <code>php artisan vendor:publish --tag=lasso-config</code>.</li>
                    <li>In <code>config/lasso.php</code> change <code>'disk' => 'assets'</code> to <code>'disk' => 's3'</code> to use the Amazon S3 drivers.</li>
                    <li>
                        Optional, change <code>upload_to</code> if you plan on using the same bucket for multiple sites.
                        You'll notice in this repository I've set it to <code>lasso_ci_cd/lasso</code>.
                    </li>
                </ul>

                <h3>Configure .gitignore</h3>
                <p>Since the whole point of this is to no longer commit our assets to GitHub we need to update our <code>.gitignore</code> file as well.</p>

                <ul>
                    <li>Add Lasso's temp directory <code>.lasso</code>.</li>
                    <li>Add our public asset directories and files.
                        <ul>
                            <li><code>/public/css/*</code></li>
                            <li><code>/public/img/*</code></li>
                            <li><code>/public/js/*</code></li>
                        </ul>
                    </li>
                    <li>In this case we need to also include the mix manifest <code>/public/mix-manifest.json</code></li>
                </ul>

                <h2 id="github-actions">GitHub Actions</h2>
                <p>
                    To get us started we're going to create and run two workflows, one to do our testing (CI) and one to do our ploying (CD).
                    These workflows are based on the <a href="https://github.com/actions/starter-workflows/blob/48d91f58fdbd01a65b0e1e4dcc0eda76d3540536/ci/laravel.yml" target="_blank" rel="noopener">Laravel</a> actions starter workflows.
                </p>

                <h3>Continuous Improvement Workflow</h3>
                <p>
                    The goal of this workflow is to run when a pull request is created to run tests only.
                </p>

                <h4>Workflow Additions</h4>
                <p>
                    We're only going to add one section here to make sure our NPM dependencies install and package correctly.
                    After the <code>Copy .env</code> step add the <code>Install npm dependencies and package</code> step to your workflow.
                </p>

                <blockquote>
                    <p>
                        The workflow can be found in <a href="https://github.com/alexjustesen/lasso-ci-cd/blob/develop/.github/workflows/laravel-ci.yml" target="_blank" rel="noopener">laravel-ci.yml</a>.
                    </p>
                </blockquote>

                <h3>Continuous Deployment Workflow</h3>
                <p>
                    The goal of this workflow is to run when a pull request is merged into develop, run tests, compile and upload assets and deploy the code.
                </p>

                <h4>Using Secrets</h4>
                <blockquote>
                    <p>
                        Important: Never hard code passwords or tokens into your code, use secrets.
                    </p>
                </blockquote>

                <p>
                    Before we can use secrets in our workflow we need to create them in the repository. Go to Settings -> Secrets in your repository and add the following variables.
                </p>

                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Value</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>AWS_ACCESS_KEY_ID</td>
                            <td>Also known as the public key to your account or API user.</td>
                        </tr>

                        <tr>
                            <td>AWS_SECRET_ACCESS_KEY</td>
                            <td>Also known as the secret key to your account or API user.</td>
                        </tr>

                        <tr>
                            <td>AWS_DEFAULT_REGION</td>
                            <td>The default region where you bucket resides.</td>
                        </tr>

                        <tr>
                            <td>AWS_BUCKET</td>
                            <td>The buckets name.</td>
                        </tr>

                        <tr>
                            <td>AWS_ENDPOINT</td>
                            <td>The endpoint should be a fully qualified domain name including the protocol (https://).</td>
                        </tr>
                    </tbody>
                </table>

                <blockquote>
                    <p>Note: I use Backblaze's B2 service because it's cheaper than AWS.</p>
                </blockquote>

                <p>
                    In addition to the changes we made to test our NPM dependencies we'll add additional steps to complete the process.
                </p>

                <p>
                    We're going to add a new step to compile and upload our assets to our S3 bucket and we'll call
                    it <code>Publish assets with Lasso</code>.
                </p>

                <blockquote>
                    <p>
                        The workflow can be found in <a href="https://github.com/alexjustesen/lasso-ci-cd/blob/develop/.github/workflows/laravel-cd.yml" target="_blank" rel="noopener">laravel-cd.yml</a>.
                    </p>
                </blockquote>

                <h2 id="deployment">Deployment</h2>

                <p>
                    Lastly you need to add <code>php artisan lasso:pull</code> to your deployment script, I suggest doing this prior to clearing or filling any caches.
                    Almost forgot, don't forget to update your S3 variables in your <code>.env</code> file.
                </p>

                <p>
                    Well, that's pretty much it. At this point you should be up and running, should you have an issues getting this setup
                    <a href="https://twitter.com/alexjustesen" target="_blank" rel="noopener">Twitter</a> is the best way to reach me.
                </p>

                <h3>Bonus</h3>
                <p>
                    I use <a href="https://ploi.io/register?referrer=Q6l9H3OiyYW58yEcwjy8">Ploi.io</a> <em>(referral link)</em> to host my Laravel applications.
                    Check out Ploi's docs to see how you can trigger a deployment and reference the step called <code>Trigger ploi.io deployment</code> in <code>.github/workflows/develop.yml</code>.
                </p>

                <blockquote>
                    <p>You can also do this in Laravel Forge as well as other platforms.</p>
                </blockquote>

                <p>
                    To trigger a deployment in Ploi you need to send a <code>POST</code> request to a webhook URL. You'll notice for this project I trigger a deployment from our workflow utilizing an additional secret.
                </p>

                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Value</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>PLOI_DEPLOYMENT_WEBHOOK</td>
                            <td>The webhook URL can be found in your site's repository settings in the Ploi panel.</td>
                        </tr>
                    </tbody>
                </table>

                <p>
                    Now when your CD workflow runs successfully it will trigger a deployment automatically on Ploi.
                </p>

                <h2 id="disclaimer">Disclaimer</h2>
                <p>
                    CI/CD pipelines have many forms, each is specific to the needs of the project and <strong>should evolve over time</strong>.
                    This is just one way of doing it, find the process that works for you.
                </p>

                <h2 id="about-me">About me</h2>
                <p>
                    I'm Alex, I've done some form of web development for the better part of 15 years. I currently work for <a href="https://massmutual.com" target="_blank" rel="noopener">MassMutual</a> doing operational
                    data architecture and web development for our insurance business lines.
                </p>

                <p>
                    I also do a bit of feelance work to pay for 🍺 and 🚗.
                </p>

                <p>You can get ahold of me through the methods below.</p>

                <ul>
                    <li><a href="https://github.com/alexjustesen" target="_blank" rel="noopener">GitHub</a></li>
                    <li><a href="https://twitter.com/alexjustesen" target="_blank" rel="noopener">Twitter</a></li>
                    <li><a href="https://www.linkedin.com/in/alexander-justesen/" target="_blank" rel="noopener">LinkedIn</a></li>
                </ul>
            </article>

            <div class="mt-8 text-sm text-gray-700">
                This was last updated on {{ \App\Application::DATE }} as v{{ \App\Application::VERSION }}.
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
