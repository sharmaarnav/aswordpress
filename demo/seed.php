<?php
/**
 * Sample notes for Schematic.
 *
 * Run from a WordPress install where the theme is available:
 * wp eval-file /path/to/demo/seed.php
 *
 * @package Schematic
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with: wp eval-file demo/seed.php\n" );
	return;
}

/**
 * Create a term if the slug is free.
 *
 * @param string $taxonomy    Taxonomy name.
 * @param string $name        Term name.
 * @param string $slug        Term slug.
 * @param string $description Term description.
 * @return int
 */
function schematic_demo_term( $taxonomy, $name, $slug, $description = '' ) {
	$existing = get_term_by( 'slug', $slug, $taxonomy );
	if ( $existing && ! is_wp_error( $existing ) ) {
		return (int) $existing->term_id;
	}

	$result = wp_insert_term(
		$name,
		$taxonomy,
		array(
			'slug'        => $slug,
			'description' => $description,
		)
	);

	if ( is_wp_error( $result ) ) {
		fwrite( STDERR, $result->get_error_message() . PHP_EOL );
		return 0;
	}

	return (int) $result['term_id'];
}

/**
 * Insert a note once, keyed by slug.
 *
 * @param array $args   wp_insert_post args.
 * @param int[] $cats   Category IDs.
 * @param int   $series Series term ID.
 * @return int
 */
function schematic_demo_post( $args, $cats, $series = 0 ) {
	$found = get_posts(
		array(
			'name'           => $args['post_name'],
			'post_type'      => 'post',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	if ( $found ) {
		return (int) $found[0];
	}

	$post_id = wp_insert_post(
		array_merge(
			array(
				'post_status' => 'publish',
				'post_type'   => 'post',
			),
			$args
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		fwrite( STDERR, $post_id->get_error_message() . PHP_EOL );
		return 0;
	}

	wp_set_post_categories( $post_id, $cats );
	if ( ! empty( $args['tags'] ) ) {
		wp_set_post_tags( $post_id, $args['tags'] );
	}
	if ( $series ) {
		wp_set_object_terms( $post_id, array( (int) $series ), 'series' );
	}

	return (int) $post_id;
}

/**
 * Attach a PNG as the featured image when the file exists and none is set.
 *
 * @param int    $post_id Post ID.
 * @param string $path    Absolute path.
 * @param string $title   Attachment title.
 */
function schematic_demo_thumbnail( $post_id, $path, $title ) {
	if ( ! $post_id || ! file_exists( $path ) || has_post_thumbnail( $post_id ) ) {
		return;
	}

	$bits = wp_upload_bits( basename( $path ), null, file_get_contents( $path ) );
	if ( ! empty( $bits['error'] ) ) {
		fwrite( STDERR, $bits['error'] . PHP_EOL );
		return;
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/png',
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$bits['file'],
		$post_id
	);

	if ( is_wp_error( $attachment_id ) ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $bits['file'] ) );
	set_post_thumbnail( $post_id, $attachment_id );
	wp_update_post(
		array(
			'ID'           => $attachment_id,
			'post_excerpt' => $title,
		)
	);
}

if ( 'Just another WordPress site' === get_option( 'blogdescription' ) || '' === get_option( 'blogdescription' ) ) {
	update_option( 'blogdescription', 'Field notes on software, systems, and the craft of explanation.' );
}

$hello = get_page_by_path( 'hello-world', OBJECT, 'post' );
if ( $hello && false !== strpos( $hello->post_content, 'Welcome to WordPress' ) ) {
	wp_delete_post( $hello->ID, true );
}

$sample_page = get_page_by_path( 'sample-page' );
if ( $sample_page && 'Sample Page' === $sample_page->post_title ) {
	wp_delete_post( $sample_page->ID, true );
}

$compilers = schematic_demo_term( 'category', 'Compilers', 'compilers', 'Intermediate representations, lowering, and the passes between them.' );
$runtime   = schematic_demo_term( 'category', 'Runtime', 'runtime', 'What a program is allowed to do while it is running.' );
$systems   = schematic_demo_term( 'category', 'Systems', 'systems', 'Queues, failure, and the shape of traffic.' );
$ops       = schematic_demo_term( 'category', 'Operations', 'operations', 'How people run what other people shipped.' );
$apis      = schematic_demo_term( 'category', 'Interfaces', 'interfaces', 'Defaults, names, and the contracts around them.' );
$series    = schematic_demo_term( 'series', 'Compiler notes', 'compiler-notes', 'A short series on intermediate representations and the checks around them.' );

$images = dirname( __FILE__ ) . '/images';

$ir = schematic_demo_post(
	array(
		'post_name'    => 'boring-intermediate-representation',
		'post_title'   => 'Why your intermediate representation should be boring',
		'post_date'    => '2026-09-02 09:00:00',
		'post_excerpt' => 'An IR earns its keep by being unsurprising. When it is clever, every pass starts with a negotiation.',
		'tags'         => array( 'llvm', 'compilers' ),
		'post_content' => <<<'HTML'
<!-- wp:paragraph -->
<p>An intermediate representation earns its keep by being unsurprising. When the IR is clever, every pass starts with a negotiation: which flags are live, which operands are decorative, which pseudo-instructions exist only because a backend asked for them in 2014.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Pick a small instruction set</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Prefer a dozen instructions you can explain on a whiteboard to a hundred that encode optimizations you no longer run. A small set makes dumps readable, and readable dumps are how you debug a miscompile at 1 a.m.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">What to leave out</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><li>Addressing modes that only one frontend emits</li><li>Flags that mean three things depending on a mode bit</li><li>Pseudo-ops that expand differently in debug builds</li></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Make ownership visible</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Every value should have one producer. If a pass needs a temporary, it should look like a temporary, not like a source-level name that escaped. Single assignment is not an aesthetic. It is how you keep “where did this come from?” a local question.</p>
<!-- /wp:paragraph -->

<!-- wp:code -->
<pre class="wp-block-code"><code class="language-rust">fn lower_add(lhs: Value, rhs: Value) -> Value {
    builder.emit(Inst::Add { lhs, rhs })
}</code></pre>
<!-- /wp:code -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Optimize later, and narrowly</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Strength reduction and inlining are visitors. They should not require a second IR. If a transform cannot be stated as “match this shape, replace it with that shape,” it is probably a new language wearing a pass’s coat.</p>
<!-- /wp:paragraph -->

<!-- wp:quote {"className":"is-style-schematic-pull"} -->
<blockquote class="wp-block-quote is-style-schematic-pull"><!-- wp:paragraph -->
<p>The IR is a contract between passes. Contracts that change every quarter are how compilers rot.</p>
<!-- /wp:paragraph --></blockquote>
<!-- /wp:quote -->

<!-- wp:heading -->
<h2 class="wp-block-heading">A practical test</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Hand a new teammate a dump of twenty instructions and ask them what the function returns. Time them. If the answer needs a legend, the IR is doing too much.</p>
<!-- /wp:paragraph -->

<!-- wp:table -->
<figure class="wp-block-table"><table><thead><tr><th>Signal</th><th>Boring IR</th><th>Clever IR</th></tr></thead><tbody><tr><td>Newcomer reads a dump</td><td>Minutes</td><td>After the style guide</td></tr><tr><td>Pass ordering</td><td>Mostly commutative</td><td>Load-bearing</td></tr><tr><td>Debug info</td><td>Attached, not entangled</td><td>Woven through operands</td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:group {"className":"is-style-schematic-note"} -->
<div class="wp-block-group is-style-schematic-note"><!-- wp:paragraph -->
<p><strong>Note.</strong> Boring does not mean untyped. Types are how the IR refuses states you did not mean to represent.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
HTML
	),
	array( $compilers ),
	$series
);
schematic_demo_thumbnail( $ir, $images . '/ir-dump.png', 'A short dump: two adds, one branch, no legend.' );

$bounds = schematic_demo_post(
	array(
		'post_name'    => 'bounds-checks-are-a-product-decision',
		'post_title'   => 'Bounds checks are a product decision',
		'post_date'    => '2026-09-18 09:00:00',
		'post_excerpt' => 'Eliminating a check is not a compiler trophy. It is a promise about who gets paged when the promise is wrong.',
		'tags'         => array( 'runtime', 'safety' ),
		'post_content' => <<<'HTML'
<!-- wp:paragraph -->
<p>Eliminating a bounds check is not a compiler trophy. It is a promise about who gets paged when the index is wrong. The check is a product decision that happens to be implemented in the lowering pass.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">The cost is not the branch</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>On a modern core the branch itself is cheap when it is never taken. What is expensive is the story you tell the rest of the optimizer. A check that the prover cannot see becomes a wall: nothing after it can be hoisted, and every caller inherits the doubt.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Where the proof should live</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Put the proof next to the index arithmetic, in the IR, as a fact other passes can read. A comment in the frontend does not survive inlining. A fact attached to the value does.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Facts worth recording</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><li>The length is invariant across the loop</li><li>The index was already compared against that length</li><li>The slice was reborrowed, not rebuilt</li></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Leave a check when the story is thin</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>If the only reason to delete the check is that a benchmark got 3% faster, keep it. Ship the check, document the hot path, and delete it when a pass can explain the proof to the next person who reads the dump.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"is-style-schematic-warning"} -->
<div class="wp-block-group is-style-schematic-warning"><!-- wp:paragraph -->
<p><strong>Warning.</strong> A check removed under <code>unsafe</code> without a fact in the IR will be reintroduced by the next refactor, or it will not, and you will not know which.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
HTML
	),
	array( $runtime ),
	$series
);

schematic_demo_post(
	array(
		'post_name'    => 'field-guide-to-backpressure',
		'post_title'   => 'A field guide to backpressure',
		'post_date'    => '2026-10-01 09:00:00',
		'post_excerpt' => 'A slow consumer has to be able to say wait. Buffering without a bound is just a delayed outage.',
		'tags'         => array( 'queues', 'http' ),
		'post_content' => <<<'HTML'
<!-- wp:paragraph -->
<p>Backpressure is a signal from a slow consumer that asks producers to wait. Without it, every queue in the path becomes a place to hide latency until the process runs out of memory and the outage looks sudden.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Name the bound</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Every queue needs a number you can defend: requests, bytes, or time. “We’ll scale” is not a bound. Write the number next to the queue, and write what happens when it is reached. The choices are small: reject, shed, or wait.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Propagate the wait</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Waiting only works if the caller can wait too. A thread pool that absorbs the delay locally has not applied backpressure. It has stored it. The signal has to cross the RPC boundary as a status the client is required to honor.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Statuses that count</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><li>Retry-after, with a clock the client actually reads</li><li>A load-shed response that is cheaper than the work it refuses</li><li>A queue-length metric that pages a person before the process dies</li></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Watch the oldest item</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Depth lies. A queue of ten items can be healthy, and a queue of ten items can be a stall if the oldest one has been waiting for a minute. Age is the metric that matches the user’s experience.</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"className":"schematic-def"} -->
<div class="wp-block-columns schematic-def"><!-- wp:column {"width":"32%"} -->
<div class="wp-block-column" style="flex-basis:32%"><!-- wp:paragraph -->
<p><strong>Backpressure</strong></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<!-- wp:column {"width":"68%"} -->
<div class="wp-block-column" style="flex-basis:68%"><!-- wp:paragraph -->
<p>A signal from a slow consumer that asks producers to wait, rather than buffering without bound.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
HTML
	),
	array( $systems )
);

schematic_demo_post(
	array(
		'post_name'    => 'runbooks-people-open',
		'post_title'   => 'Notes on writing runbooks people actually open',
		'post_date'    => '2026-10-09 09:00:00',
		'post_excerpt' => 'A runbook is a program for a tired human. It should tell them what to type, what they should see, and when to stop.',
		'tags'         => array( 'sre', 'on-call' ),
		'post_content' => <<<'HTML'
<!-- wp:paragraph -->
<p>A runbook is a program for a tired human. If it begins with architecture, it will be skipped. The first screen should answer three questions: what is broken, what do I type, and what should I see if it worked.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Lead with the command</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Put the exact command in the first section. Qualify the cluster, the namespace, and the account. A command that works only on the author’s laptop is a diary entry.</p>
<!-- /wp:paragraph -->

<!-- wp:code -->
<pre class="wp-block-code"><code class="language-bash">kubectl -n payments get pods -l app=ledger
kubectl -n payments logs deploy/ledger --since=15m | head</code></pre>
<!-- /wp:code -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Say what “fine” looks like</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Every step needs an expected observation. “Check the dashboard” is not an observation. “Error ratio under 0.1% for five minutes, and the oldest queue item under two seconds” is.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Write the stop condition</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The most useful line in a runbook is the one that says to stop and escalate. People in an incident will keep typing. Give them permission to hand it over, and say who is actually awake.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"is-style-schematic-note"} -->
<div class="wp-block-group is-style-schematic-note"><!-- wp:paragraph -->
<p><strong>Note.</strong> If a step has not been run in ninety days, delete it or schedule a drill. Untested runbooks rot in the same way unused feature flags do.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
HTML
	),
	array( $ops )
);

$defaults = schematic_demo_post(
	array(
		'post_name'    => 'the-cost-of-a-convenient-default',
		'post_title'   => 'The cost of a convenient default',
		'post_date'    => '2026-10-16 09:00:00',
		'post_excerpt' => 'A default is a decision you make for everyone who will not read the docs. Choose it as if you will be on call for it.',
		'tags'         => array( 'api', 'defaults' ),
		'post_content' => <<<'HTML'
<!-- wp:paragraph -->
<p>A default is a decision you make for everyone who will not read the documentation. Most callers will not. The default is the product, and the configuration page is a museum of arguments you already lost.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Defaults become load</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>A retry default of “forever, immediately” is a traffic amplifier with a friendly name. A timeout default of “none” is a thread leak. Write the default down as the behavior under failure, not as the behavior under the demo.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Make the safe path the short path</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>If the safe choice needs three flags and the dangerous choice needs zero, you have chosen the dangerous default and decorated it. Flip them. People will copy the first snippet in the guide, and that snippet should be the one you are willing to run in production.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Questions for a new default</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><li>What happens when the dependency is slow, not merely down?</li><li>Who pays for a retry storm: the caller, the callee, or a neighbor?</li><li>Can a caller discover the default without reading source?</li></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Change defaults in public</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Changing a default is an API change even when the type of the field did not move. Name the version, show the old snippet next to the new one, and keep the old behavior available until the callers you know about have shipped.</p>
<!-- /wp:paragraph -->

<!-- wp:quote {"className":"is-style-schematic-pull"} -->
<blockquote class="wp-block-quote is-style-schematic-pull"><!-- wp:paragraph -->
<p>Choose the default as if you will be on call for everyone who accepts it.</p>
<!-- /wp:paragraph --></blockquote>
<!-- /wp:quote -->
HTML
	),
	array( $apis )
);
schematic_demo_thumbnail( $defaults, $images . '/default-path.png', 'The short path and the safe path, drawn as the same line.' );

$about = get_page_by_path( 'about' );
if ( ! $about ) {
	wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_name'    => 'about',
			'post_title'   => 'About',
			'post_content' => <<<'HTML'
<!-- wp:paragraph -->
<p>Schematic is a journal of working notes: compilers, runtimes, and the operational facts that sit beside them. Pieces are written to be read slowly, with the code nearby.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">How a note is filed</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Each note has a topic, an optional series, and a deck. The deck is the excerpt you write by hand. Series are ordered by the day they were published, oldest first, so “part 1” stays part 1.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Colophon</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The theme is set in Fraunces, Source Serif 4, and IBM Plex Mono. The oxide rule at the top of the page is the reading progress of a note.</p>
<!-- /wp:paragraph -->
HTML
		)
	);
}

$menu = wp_get_nav_menu_object( 'Primary' );
if ( ! $menu ) {
	$menu_id = wp_create_nav_menu( 'Primary' );
} else {
	$menu_id = (int) $menu->term_id;
}

$existing_items = wp_get_nav_menu_items( $menu_id );
if ( empty( $existing_items ) && $menu_id && ! is_wp_error( $menu_id ) ) {
	$position = 1;
	foreach (
		array(
			array( 'category', $compilers, 'Compilers' ),
			array( 'category', $systems, 'Systems' ),
			array( 'category', $ops, 'Operations' ),
			array( 'series', $series, 'Compiler notes' ),
		) as $item
	) {
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => $item[2],
				'menu-item-object'    => $item[0],
				'menu-item-object-id' => (int) $item[1],
				'menu-item-type'      => 'taxonomy',
				'menu-item-status'    => 'publish',
				'menu-item-position'  => $position,
			)
		);
		++$position;
	}

	$about_page = get_page_by_path( 'about' );
	if ( $about_page ) {
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => 'About',
				'menu-item-object'    => 'page',
				'menu-item-object-id' => (int) $about_page->ID,
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
				'menu-item-position'  => $position,
			)
		);
	}
}

$locations            = get_theme_mod( 'nav_menu_locations', array() );
$locations['primary'] = (int) $menu_id;
set_theme_mod( 'nav_menu_locations', $locations );

if ( $defaults ) {
	$comments = get_comments(
		array(
			'post_id' => $defaults,
			'status'  => 'all',
			'count'   => true,
		)
	);
	if ( ! $comments ) {
		wp_insert_comment(
			array(
				'comment_post_ID'      => $defaults,
				'comment_author'       => 'Mina',
				'comment_author_email' => 'mina@example.com',
				'comment_content'      => 'We learned this the hard way with a client timeout of zero. The default was copied into four services before anyone noticed the threads.',
				'comment_approved'     => 1,
			)
		);
	}
}

fwrite( STDOUT, "Schematic sample notes are in place.\n" );
