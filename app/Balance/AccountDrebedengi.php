<?php

declare(strict_types=1);

namespace App\Balance;

/**
 * Drebedengi account model.
 *
 * @package    App\Balance
 * @author     Nikolaj Rudakov <nnrudakov@gmail.com>
 * @copyright  2018-2019
 */
class AccountDrebedengi extends Account
{
    /**
     * Account name.
     *
     * @var string
     */
    public const NAME = 'drebedengi';
    /**
     * Account title.
     *
     * @var string
     */
    public const TITLE = 'Дребеденьги';
    /**
     * Operation type `income`.
     *
     * @var integer
     */
    public const TYPE_INCOME = 2;
    /**
     * Operation type `waste`.
     *
     * @var integer
     */
    public const TYPE_WASTE = 3;
    /**
     * Operation type `move`.
     *
     * @var integer
     */
    public const TYPE_MOVE = 4;
    /**
     * Operation type `change`.
     *
     * @var integer
     */
    public const TYPE_CHANGE = 5;
    /**
     * Category for food.
     *
     * @var string
     */
    private const CATEGORY_FOOD = 'Еда и продукты';
    /**
     * Category for meds.
     *
     * @var string
     */
    private const CATEGORY_MEDS = 'Аптека';
    /**
     * Category for treatment.
     *
     * @var string
     */
    private const CATEGORY_TREATMENT = 'Анализы и лечение';
    /**
     * Category for fuel.
     *
     * @var string
     */
    private const CATEGORY_FUEL = 'Бензин';
    /**
     * Category for snack, cafe.
     *
     * @var string
     */
    private const CATEGORY_FASTFOOD = 'Обеды, перекусы';
    /**
     * Category for flat.
     *
     * @var string
     */
    private const CATEGORY_FLAT = 'Быт';
    /**
     * Category for electricity.
     *
     * @var string
     */
    private const CATEGORY_POWER = 'Коммунальнальные платежи';
    /**
     * Category for car.
     *
     * @var string
     */
    private const CATEGORY_CAR = 'Автомобиль';
    /**
     * Category for electricity.
     *
     * @var string
     */
    private const CATEGORY_CARPARTS = 'Запчасти, ремонт';
    /**
     * Category for appliances.
     *
     * @var string
     */
    private const CATEGORY_APPLIANCES = 'Бытовая техника';
    /**
     * Category for other expences.
     *
     * @var string
     */
    private const CATEGORY_OTHER = 'Другие траты';
    /**
     * Category for fare.
     *
     * @var string
     */
    private const CATEGORY_FARE = 'Проезд';
    /**
     * Category for home.
     *
     * @var string
     */
    private const CATEGORY_HOME = 'Дом';
    /**
     * Category for equipment.
     *
     * @var string
     */
    private const CATEGORY_HOMEEQUIPMENT = 'Инструменты';
    /**
     * Category for utility bills.
     *
     * @var string
     */
    private const CATEGORY_HOMEBILLS = 'Коммунальные';
    /**
     * Place for cash.
     *
     * @var string
     */
    private const PLACE_CASH = 'Наличные';
    /**
     * Place for Alfa-Bank.
     *
     * @var string
     */
    private const PLACE_ALFARUB = 'Рублы';

    protected $table = 'accounts';

    public static function query()
    {
        return parent::query()->where('name', static::NAME);
    }

    /**
     * Get food category ID.
     *
     * @reurn integer
     */
    public function getCategoryFood(): int
    {
        $category = $this->findCategory(static::CATEGORY_FOOD);

        return $category ? (int) $category->id : 0;
    }

    /**
     * Get drugstore category ID.
     *
     * @reurn integer
     */
    public function getCategoryMeds(): int
    {
        $category = $this->findCategory(static::CATEGORY_MEDS);

        return $category ? (int) $category->id : 0;
    }

    /**
     * Get medical treatment category ID.
     *
     * @reurn integer
     */
    public function getCategoryMedTreatment(): int
    {
        $category = $this->findCategory(static::CATEGORY_TREATMENT);

        return $category ? (int) $category->id : 0;
    }

    /**
     * Get fuel category ID.
     *
     * @reurn integer
     */
    public function getCategoryFuel(): int
    {
        $category = $this->findCategory(static::CATEGORY_FUEL);

        return $category ? (int) $category->id : 0;
    }

    /**
     * Get fast-food category ID.
     *
     * @reurn integer
     */
    public function getCategoryFastFood(): int
    {
        $category = $this->findCategory(static::CATEGORY_FASTFOOD);

        return $category ? (int) $category->id : 0;
    }

    /**
     * Get electricity category ID.
     *
     * @reurn integer
     */
    public function getCategoryPower(): int
    {
        if ($parent = $this->findCategory(static::CATEGORY_FLAT)) {
            $category = $this->findCategory(static::CATEGORY_POWER, (int) $parent->id);

            return $category ? (int) $category->id : 0;
        }

        return 0;
    }

    /**
     * Get car parts category ID.
     *
     * @reurn integer
     */
    public function getCategoryCarParts(): int
    {
        if ($parent = $this->findCategory(static::CATEGORY_CAR)) {
            $category = $this->findCategory(static::CATEGORY_CARPARTS, (int) $parent->id);

            return $category ? (int) $category->id : 0;
        }

        return 0;
    }

    /**
     * Get appliances category ID.
     *
     * @reurn integer
     */
    public function getCategoryAppliances(): int
    {
        $category = $this->findCategory(static::CATEGORY_APPLIANCES);

        return $category ? (int) $category->id : 0;
    }

    /**
     * Get fare category ID.
     *
     * @reurn integer
     */
    public function getCategoryFare(): int
    {
        if ($parent = $this->findCategory(static::CATEGORY_OTHER)) {
            $category = $this->findCategory(static::CATEGORY_FARE, (int) $parent->id);

            return $category ? (int) $category->id : 0;
        }

        return 0;
    }

    /**
     * Get home equipment category ID.
     *
     * @reurn integer
     */
    public function getCategoryHomeEquipment(): int
    {
        if ($parent = $this->findCategory(static::CATEGORY_HOME)) {
            $category = $this->findCategory(static::CATEGORY_HOMEEQUIPMENT, (int) $parent->id);

            return $category ? (int) $category->id : 0;
        }

        return 0;
    }

    /**
     * Get home bills category ID.
     *
     * @reurn integer
     */
    public function getCategoryHomeBills(): int
    {
        if ($parent = $this->findCategory(static::CATEGORY_HOME)) {
            $category = $this->findCategory(static::CATEGORY_HOMEBILLS, (int) $parent->id);

            return $category ? (int) $category->id : 0;
        }

        return 0;
    }

    /**
     * Get cash place.
     *
     * @reurn integer
     */
    public function getPlaceCash(): int
    {
        $place = $this->findPlace(static::PLACE_CASH);

        return $place ? (int) $place->id : 0;
    }

    /**
     * Get Yandex place.
     *
     * @param string $name Place name.
     *
     * @reurn integer
     */
    public function getPlaceYandex(string $name): int
    {
        $place = $this->findPlace($name);

        return $place ? (int) $place->id : 0;
    }

    /**
     * Get Alfa-Bank RUB place.
     *
     * @reurn integer
     */
    public function getPlaceAlfaRub(): int
    {
        $place = $this->findPlace(static::PLACE_ALFARUB);

        return $place ? (int) $place->id : 0;
    }

    /**
     * Find category by name.
     *
     * @param string  $name     Name.
     * @param integer $parentId Parent ID.
     *
     * @return \stdClass
     */
    private function findCategory(string $name, int $parentId = 0): ?\stdClass
    {
        /** @noinspection ForeachSourceInspection */
        foreach ($this->data->categories as $category) {
            if ($category->name === $name) {
                if ($parentId) {
                    if ($parentId === (int) $category->parent_id) {
                        return $category;
                    }
                    continue;
                }

                return $category;
            }
        }

        return null;
    }

    /**
     * Find place by name.
     *
     * @param string $name Name.
     *
     * @return \stdClass
     */
    private function findPlace(string $name): ?\stdClass
    {
        /** @noinspection ForeachSourceInspection */
        foreach ($this->data->places as $place) {
            if ($place->name === $name) {
                return $place;
            }
        }

        return null;
    }
}
