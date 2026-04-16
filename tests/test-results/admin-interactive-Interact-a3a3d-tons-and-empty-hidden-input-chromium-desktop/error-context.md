# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: admin/interactive.spec.js >> Interactive Kitchen Sink >> Media Library >> renders with select and clear buttons and empty hidden input
- Location: tests/e2e/specs/admin/interactive.spec.js:240:3

# Error details

```
Error: expect(locator).toBeVisible() failed

Locator:  locator('.pc-settings-media-clear[data-key="media_image"]')
Expected: visible
Received: hidden
Timeout:  5000ms

Call log:
  - Expect "toBeVisible" with timeout 5000ms
  - waiting for locator('.pc-settings-media-clear[data-key="media_image"]')
    9 × locator resolved to <button type="button" title="Remove" data-key="media_image" class="button pc-settings-media-clear">×</button>
      - unexpected value "hidden"

```

# Test source

```ts
  155 | 
  156 | 			// Select a post.
  157 | 			await search.fill( 'E2E' );
  158 | 			await expect(
  159 | 				picker.locator( '.pc-picker__dropdown' )
  160 | 			).toBeVisible( { timeout: 5000 } );
  161 | 			await picker
  162 | 				.locator( '.pc-picker__option', { hasText: 'E2E Test Post' } )
  163 | 				.first()
  164 | 				.click();
  165 | 			await expect( picker.locator( '.pc-picker__tag' ) ).toBeVisible();
  166 | 
  167 | 			// Remove it.
  168 | 			await picker.locator( '.pc-picker__tag-remove' ).click();
  169 | 
  170 | 			// Tag gone, hidden input empty.
  171 | 			await expect( picker.locator( '.pc-picker__tag' ) ).toHaveCount(
  172 | 				0
  173 | 			);
  174 | 			await expect(
  175 | 				picker.locator( 'input[data-picker-input="pick_post"]' )
  176 | 			).toHaveValue( '' );
  177 | 		} );
  178 | 	} );
  179 | 
  180 | 	// ─────────────────────────────────────────────────────────────────
  181 | 	// User Picker
  182 | 	// ─────────────────────────────────────────────────────────────────
  183 | 	test.describe( 'User Picker', () => {
  184 | 		test( 'renders with search input', async ( { page } ) => {
  185 | 			const picker = page.locator(
  186 | 				'.pc-picker[data-picker-key="pick_user"]'
  187 | 			);
  188 | 			await expect( picker ).toBeVisible();
  189 | 			await expect( picker ).toHaveAttribute(
  190 | 				'data-picker-type',
  191 | 				'user'
  192 | 			);
  193 | 		} );
  194 | 
  195 | 		test( 'searching for "admin" shows the default admin user', async ( {
  196 | 			page,
  197 | 		} ) => {
  198 | 			const picker = page.locator(
  199 | 				'.pc-picker[data-picker-key="pick_user"]'
  200 | 			);
  201 | 			const search = picker.locator( '.pc-picker__search' );
  202 | 
  203 | 			await search.fill( 'admin' );
  204 | 
  205 | 			const dropdown = picker.locator( '.pc-picker__dropdown' );
  206 | 			await expect( dropdown ).toBeVisible( { timeout: 5000 } );
  207 | 
  208 | 			// wp-env creates an admin user with display_name "admin".
  209 | 			await expect(
  210 | 				dropdown.locator( '.pc-picker__option' ).first()
  211 | 			).toBeVisible();
  212 | 		} );
  213 | 
  214 | 		test( 'selecting a user and submitting persists the user ID', async ( {
  215 | 			page,
  216 | 		} ) => {
  217 | 			const picker = page.locator(
  218 | 				'.pc-picker[data-picker-key="pick_user"]'
  219 | 			);
  220 | 			const search = picker.locator( '.pc-picker__search' );
  221 | 
  222 | 			await search.fill( 'admin' );
  223 | 			const dropdown = picker.locator( '.pc-picker__dropdown' );
  224 | 			await expect( dropdown ).toBeVisible( { timeout: 5000 } );
  225 | 
  226 | 			await dropdown.locator( '.pc-picker__option' ).first().click();
  227 | 			await expect( picker.locator( '.pc-picker__tag' ) ).toBeVisible();
  228 | 
  229 | 			await submitForm( page );
  230 | 			const stored = await readDump( page );
  231 | 
  232 | 			expect( Number( stored.pick_user ) ).toBeGreaterThan( 0 );
  233 | 		} );
  234 | 	} );
  235 | 
  236 | 	// ─────────────────────────────────────────────────────────────────
  237 | 	// Media Library
  238 | 	// ─────────────────────────────────────────────────────────────────
  239 | 	test.describe( 'Media Library', () => {
  240 | 		test( 'renders with select and clear buttons and empty hidden input', async ( {
  241 | 			page,
  242 | 		} ) => {
  243 | 			await expect(
  244 | 				page.locator( '#media_upload_media_image' )
  245 | 			).toBeVisible();
  246 | 			await expect(
  247 | 				page.locator(
  248 | 					'.pc-settings-media-select[data-key="media_image"]'
  249 | 				)
  250 | 			).toBeVisible();
  251 | 			await expect(
  252 | 				page.locator(
  253 | 					'.pc-settings-media-clear[data-key="media_image"]'
  254 | 				)
> 255 | 			).toBeVisible();
      |      ^ Error: expect(locator).toBeVisible() failed
  256 | 			await expect(
  257 | 				page.locator(
  258 | 					'input[type="hidden"][name="media_image"]'
  259 | 				)
  260 | 			).toHaveValue( '' );
  261 | 		} );
  262 | 
  263 | 		test( 'clicking Select opens the WP media modal', async ( {
  264 | 			page,
  265 | 		} ) => {
  266 | 			await page.click(
  267 | 				'.pc-settings-media-select[data-key="media_image"]'
  268 | 			);
  269 | 
  270 | 			// WP media modal has the class .media-modal.
  271 | 			await expect(
  272 | 				page.locator( '.media-modal' )
  273 | 			).toBeVisible( { timeout: 5000 } );
  274 | 		} );
  275 | 
  276 | 		test( 'selecting an attachment sets the hidden input and shows a preview', async ( {
  277 | 			page,
  278 | 		} ) => {
  279 | 			await page.click(
  280 | 				'.pc-settings-media-select[data-key="media_image"]'
  281 | 			);
  282 | 
  283 | 			// Wait for modal.
  284 | 			const modal = page.locator( '.media-modal' );
  285 | 			await expect( modal ).toBeVisible( { timeout: 5000 } );
  286 | 
  287 | 			// The seeded attachment should appear in the library.
  288 | 			// Click the first attachment in the grid.
  289 | 			const attachment = modal.locator( '.attachment' ).first();
  290 | 			await expect( attachment ).toBeVisible( { timeout: 5000 } );
  291 | 			await attachment.click();
  292 | 
  293 | 			// Click the "Use this media" button.
  294 | 			await page.click( '.media-button-select' );
  295 | 
  296 | 			// Modal closes.
  297 | 			await expect( modal ).not.toBeVisible();
  298 | 
  299 | 			// Hidden input now has the attachment ID.
  300 | 			const val = await page
  301 | 				.locator( 'input[type="hidden"][name="media_image"]' )
  302 | 				.inputValue();
  303 | 			expect( Number( val ) ).toBeGreaterThan( 0 );
  304 | 		} );
  305 | 
  306 | 		test( 'selected media persists after submit', async ( { page } ) => {
  307 | 			// Select an attachment.
  308 | 			await page.click(
  309 | 				'.pc-settings-media-select[data-key="media_image"]'
  310 | 			);
  311 | 			const modal = page.locator( '.media-modal' );
  312 | 			await expect( modal ).toBeVisible( { timeout: 5000 } );
  313 | 			await modal.locator( '.attachment' ).first().click();
  314 | 			await page.click( '.media-button-select' );
  315 | 			await expect( modal ).not.toBeVisible();
  316 | 
  317 | 			await submitForm( page );
  318 | 			const stored = await readDump( page );
  319 | 
  320 | 			expect( Number( stored.media_image ) ).toBeGreaterThan( 0 );
  321 | 		} );
  322 | 
  323 | 		test( 'clearing the selection empties the hidden input', async ( {
  324 | 			page,
  325 | 		} ) => {
  326 | 			// First select something.
  327 | 			await page.click(
  328 | 				'.pc-settings-media-select[data-key="media_image"]'
  329 | 			);
  330 | 			const modal = page.locator( '.media-modal' );
  331 | 			await expect( modal ).toBeVisible( { timeout: 5000 } );
  332 | 			await modal.locator( '.attachment' ).first().click();
  333 | 			await page.click( '.media-button-select' );
  334 | 			await expect( modal ).not.toBeVisible();
  335 | 
  336 | 			// Now clear.
  337 | 			await page.click(
  338 | 				'.pc-settings-media-clear[data-key="media_image"]'
  339 | 			);
  340 | 
  341 | 			await expect(
  342 | 				page.locator( 'input[type="hidden"][name="media_image"]' )
  343 | 			).toHaveValue( '' );
  344 | 		} );
  345 | 	} );
  346 | 
  347 | 	// ─────────────────────────────────────────────────────────────────
  348 | 	// WP Editor
  349 | 	// ─────────────────────────────────────────────────────────────────
  350 | 	test.describe( 'WP Editor', () => {
  351 | 		test( 'renders the editor textarea with seeded content', async ( {
  352 | 			page,
  353 | 		} ) => {
  354 | 			// WP Editor renders as a textarea (which may be hidden behind
  355 | 			// TinyMCE). The textarea always exists in the DOM.
```