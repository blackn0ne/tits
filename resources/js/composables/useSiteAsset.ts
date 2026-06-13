export function siteAsset(path: string): string {
    return `/site/assets/${path.replace(/^\//, '')}`;
}
