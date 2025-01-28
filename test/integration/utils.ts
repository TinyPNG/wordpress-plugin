import path from 'path';
import { Page } from '@playwright/test';

export async function uploadMedia(page: Page, file: string) {
  await page.goto('/wp-admin/media-new.php');
  const fileChooserPromise = page.waitForEvent('filechooser');
  await page.locator('#async-upload').click();
  const fileChooser = await fileChooserPromise;
  await fileChooser.setFiles(path.join(__dirname, `../fixtures/${file}`));
  await page.locator('#html-upload').click();
}

export async function clearMediaLibrary(page: Page) {
  await page.goto('/wp-admin/upload.php?mode=list');
  const hasNoFiles = await page.getByText('No media files found.').isVisible();
  if (hasNoFiles) {
    return;
  }
  await page.locator('#cb-select-all-1').check({ force: true });
  await page.locator('#bulk-action-selector-top').selectOption('delete');
  page.once('dialog', dialog => dialog.accept());
  await page.locator('#doaction').click();
}

export async function setAPIKey(page: Page, key = '') {
  await page.goto('/wp-admin/options-general.php?page=tinify');

  await page.waitForLoadState('networkidle');
  const changeAPIKey = await page.getByText('Change API key');
  const isVisible = await changeAPIKey.isVisible();
  if (isVisible) {
    await changeAPIKey.click();
    
  }
  await page.locator('#tinypng_api_key').fill(key);

  await page.locator('#submit').click();
}

/** Sets the compression timing to the preferred value
 * background: will upload asynchronously through ajax
 * auto: will compress before storing the file on drive
 * manual: when user clicks compress
 * @param  {Page} page the page context
 * @param  {'background'|'auto'|'manual'} timing the timing setting
 */
export async function setCompressionTiming(page: Page, timing: 'background' | 'auto' | 'manual') {
  await page.goto('/wp-admin/options-general.php?page=tinify');
  await page.locator(`#tinypng_compression_timing_${timing}`).check({ force: true });
  await page.locator('#submit').click();
}


type DefaultSizes = '0' | 'thumbnail' | 'medium' | 'medium_large' | 'large' | '1536x1536' | '2048x2048';
/**
 * @param  {Page} page the page context
 * @param  {DefaultSizes[]} sizes the sizes to enable
 * @param  {boolean=false} enableOtherSizes the state of other sizes not in the size list
 */
export async function enableCompressionSizes(page: Page, sizes: DefaultSizes[], enableOtherSizes: boolean = false) {
  await page.goto('/wp-admin/options-general.php?page=tinify');

  const allSizes = await page.locator('.sizes input[type="checkbox"]').all();
  for (const size of allSizes) {
    const sizeID = await size.getAttribute('id');
    if (!sizeID) continue;

    const sizeName = sizeID.split('tinypng_sizes_').pop();
    if (!sizeName) continue;
    
  
    if (enableOtherSizes || sizes.includes(sizeName as DefaultSizes)) {
      await size.check({ force: true });
    } else {
      await size.uncheck({ force: true });
    }
  }

  await page.locator('#submit').click();
}

type OriginalImageSettings = {
  resize: boolean;
  width?: number;
  height?: number;
  preserveDate: boolean;
  preserveCopyright: boolean;
  preserveGPS: boolean;
}
export async function setOriginalImage(page: Page, settings: OriginalImageSettings) {
  await page.goto('/wp-admin/options-general.php?page=tinify');


  if (settings.resize) {
    await page.locator('#tinypng_resize_original_enabled').check({ force: true });
    await page.fill('#tinypng_resize_original_width', settings.width?.toString() || '');
    await page.fill('#tinypng_resize_original_height', settings.height?.toString() || ''); 
  } else {
    await page.locator('#tinypng_resize_original_enabled').uncheck({ force: true });
  }

  if (settings.preserveDate) {
    await page.locator('#tinypng_preserve_data_creation').check({ force: true });
  } else {
    await page.locator('#tinypng_preserve_data_creation').uncheck({ force: true });
  }

  if (settings.preserveCopyright) {
    page.locator('#tinypng_preserve_data_copyright').check({ force: true });
  } else {
    page.locator('#tinypng_preserve_data_copyright').uncheck({ force: true });
  }

  if (settings.preserveGPS) {
    page.locator('#tinypng_preserve_data_location').check({ force: true });
  } else {
    page.locator('#tinypng_preserve_data_location').uncheck({ force: true });
  }

  await page.locator('#submit').click();
}