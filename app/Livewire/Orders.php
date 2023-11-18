<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class Orders extends Component
{
    use WithPagination;
    public $totalTimeInMinutes;


    public function mount()
    {
        $this->calculateTotalTime();
    }

    public function makeRequest()
    {
        if ($this->existRequest()) {
            return $this->cancelRequest();
        }
        if ($this->existProceedingRequest()) {
            return $this->completeRequest();
        }

        $order = Order::create([
            'user_id' => auth()->id(),
        ]);

        if (!$this->checkIFOthersWaitingProceedingRequested()) {
            $order->update(['status' => 'proceeding', 'check_in' => now()]);
            $msg = "Congratulation! Your are first one, Good luck";
        } else {
            $msg = "Request Created";
        }

        $this->reset();

        session()->flash('message', $msg);
    }

    public function cancelRequest()
    {
        Order::where([['user_id', auth()->id()], ['status', 'waiting']])->update([
            'status' => 'cancelled',
        ]);
        session()->flash('error', 'Request Cancelled');
    }

    public function proceedingRequest()
    {
        if ($this->checkIFOtherProceeding()) {
            return session()->flash('error', 'Cannot Proceding');
        }

        Order::where([['user_id', auth()->id()], ['status', 'waiting']])->update([
            'status' => 'proceeding',
            'check_in' => now(),
        ]);
        session()->flash('message', 'Request Proceding');
    }

    public function existRequest()
    {
        return Order::where([['user_id', auth()->id()], ['status', 'waiting']])->exists();
    }

    public function existProceedingRequest()
    {
        return Order::where([['user_id', auth()->id()], ['status', 'proceeding']])->exists();
    }

    public function completeRequest()
    {
        return Order::where([['user_id', auth()->id()], ['status', 'proceeding']])->first()->update([
            'status' => 'completed',
            'check_out' => now(),
        ]);
    }

    public function checkIFOthersWaitingProceedingRequested()
    {
        return Order::where('user_id', '<>', auth()->id())
            ->whereIn('status', ['waiting', 'proceeding'])
            ->exists();
    }

    public function checkIFOtherProceeding()
    {
        return Order::where('user_id', '<>', auth()->id())
            ->where('status', 'proceeding')
            ->exists();
    }

    public function calculateTotalTime()
    {
        $this->totalTimeInMinutes = Order::where('status', 'waiting')
        ->where('user_id', '<>', auth()->id())
        ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, created_at, created_at + INTERVAL 5 MINUTE)) as total_time')
        ->value('total_time');
    }

    public function render()
    {
        return view('livewire.orders', [
            'orders' => Order::with('user:id,name')->paginate(30),
            'existRequest' => $this->existRequest(),
            'existProceedingRequest' => $this->existProceedingRequest(),
        ]);
    }
}
