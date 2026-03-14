<?php
namespace Opencart\Admin\Model\Localisation;
/**
 * Minimal stub for Location model used by admin Setting pages.
 * Returns empty results to avoid loading store-related data in CMS-only build.
 */
class Location extends \Opencart\System\Engine\Model {
    /**
     * Return list of locations. For CMS-only installs we return empty array.
     *
     * @return array<int, array>
     */
    public function getLocations(): array {
        return [];
    }

    /**
     * Optional: return single location by id — not used but provided for safety.
     *
     * @param int $location_id
     * @return array<string,mixed>|null
     */
    public function getLocation(int $location_id): ?array {
        return null;
    }
}
