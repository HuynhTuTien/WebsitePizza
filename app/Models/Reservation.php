<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'note',
        'seats',
        'status',
        'reservation_date',
        'reservation_time',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function createReservation($data)
    {
        return self::create($data);
    }

    public function dishes()
    {
        return $this->belongsToMany(Dish::class, 'reservation_dish')->withPivot('quantity');
    }

    public function calculateTotalPrice()
    {
        $total = 0;
        foreach ($this->dishes as $dish) {
            $total += $dish->pivot->quantity * $dish->price;
        }
        return $total;
    }

    public static function createNewBookTable($validatedData)
    {
        $order = self::create([
            'user_id' => $validatedData['user_id'],
            'name' => $validatedData['name'],
            'note' => $validatedData['note'],
            'status' => $validatedData['status'],
            'reservation_date' => $validatedData['reservation_date'],
            'reservation_time' => $validatedData['reservation_time'],
            'seats' => $validatedData['seats'],
        ]);

        if (isset($validatedData['dish_id'])) {
            $dishIds = $validatedData['dish_id'];
            $quantities = $validatedData['quantities'] ?? [];

            foreach ($dishIds as $dishId) {
                if (isset($quantities[$dishId]) && $quantities[$dishId] > 0) {
                    $dish = Dish::find($dishId);

                    // Check if the quantity ordered exceeds available quantity
                    if ($dish->quantity < $quantities[$dishId]) {
                        throw new \Exception("Số lượng món \"{$dish->name}\" đã vượt quá số lượng có sẵn.");
                    }

                    // Deduct the quantity from available stock
                    $dish->quantity -= $quantities[$dishId];
                    $dish->save();

                    $order->dishes()->attach($dishId, ['quantity' => $quantities[$dishId]]);
                }
            }
        }

        return $order;
    }

    public function updateNewBookTable($validatedData)
    {
        $this->update([
            'user_id' => $validatedData['user_id'],
            'name' => $validatedData['name'],
            'note' => $validatedData['note'],
            'status' => $validatedData['status'],
            'reservation_date' => $validatedData['order_date'],
            'reservation_time' => $validatedData['order_time'],
            'seats' => $validatedData['seats'],
        ]);

        if (isset($validatedData['dish_id'])) {
            $dishIds = $validatedData['dish_id'];
            $quantities = $validatedData['quantities'] ?? [];

            foreach ($dishIds as $dishId) {
                $newQuantity = $quantities[$dishId];
                $existingDish = $this->dishes->find($dishId);

                if ($existingDish) {
                    $originalQuantity = $existingDish->pivot->quantity;

                    // Tính toán sự khác biệt giữa số lượng mới và số lượng ban đầu
                    $difference = $newQuantity - $originalQuantity;

                    // Cập nhật số lượng món ăn có sẵn
                    $dish = Dish::find($dishId);
                    $dish->quantity -= $difference;
                    if ($dish->quantity < 0) {
                        throw new \Exception("Số lượng món \"{$dish->name}\" đã vượt quá số lượng có sẵn.");
                    }
                    $dish->save();

                    // Cập nhật số lượng trong pivot table
                    $this->dishes()->updateExistingPivot($dishId, ['quantity' => $newQuantity]);
                } else {
                    // Thêm món mới nếu nó chưa tồn tại trong đơn đặt hàng
                    $dish = Dish::find($dishId);
                    if ($dish->quantity < $newQuantity) {
                        throw new \Exception("Số lượng món \"{$dish->name}\" đã vượt quá số lượng có sẵn.");
                    }
                    $dish->quantity -= $newQuantity;
                    $dish->save();

                    $this->dishes()->attach($dishId, ['quantity' => $newQuantity]);
                }
            }
        }

        return $this;
    }


    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($reservation) {
            $reservation->dishes()->detach();
        });
    }

    public static function createNewBookTableClient($validatedData)
    {
        $order = self::create([
            'name' => $validatedData['name'],
            'phone' => $validatedData['phone'],
            'note' => $validatedData['note'],
            'reservation_date' => $validatedData['reservation_date'],
            'reservation_time' => $validatedData['reservation_time'],
        ]);

        if (isset($validatedData['dish_id'])) {
            $dishIds = $validatedData['dish_id'];
            $quantities = $validatedData['quantities'] ?? [];

            foreach ($dishIds as $dishId) {
                if (isset($quantities[$dishId]) && $quantities[$dishId] > 0) {
                    $dish = Dish::find($dishId);

                    // Check if the quantity ordered exceeds available quantity
                    if ($dish->quantity < $quantities[$dishId]) {
                        throw new \Exception("Số lượng món \"{$dish->name}\" đã vượt quá số lượng có sẵn.");
                    }

                    // Deduct the quantity from available stock
                    $dish->quantity -= $quantities[$dishId];
                    $dish->save();

                    $order->dishes()->attach($dishId, ['quantity' => $quantities[$dishId]]);
                }
            }
        }

        return $order;
    }
}
