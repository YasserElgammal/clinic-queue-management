<div class="py-4">
    @if (Session::has('message'))
        <div class="flex items-center bg-green-500 text-white text-sm font-bold px-4 py-3 mb-4" role="alert">
            <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path
                    d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z" />
            </svg>
            <p>{{ Session::get('message') }}.</p>
        </div>
    @elseif (Session::has('error'))
        <div class="flex items-center bg-red-500 text-white text-sm font-bold px-4 py-3 mb-4" role="alert">
            <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path
                    d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z" />
            </svg>
            <p>{{ Session::get('error') }}.</p>
        </div>
    @endif
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="big-timer" id="countdown">
                {{ sprintf('%02d:%02d', floor($totalTimeInMinutes / 60), $totalTimeInMinutes % 60) }}
                </p>
            </div>
            <div class="my-4 text-center">
                <button wire:click="makeRequest"
                    class="{{ $existRequest ? 'bg-red-500 hover:bg-red-700' : 'bg-green-500  hover:bg-blue-500' }} text-white font-bold py-2 px-4 rounded">
                    {{ $existRequest ? 'Cancel Request' : ($existProceedingRequest ? 'Complete Request' : 'Make Request') }}
                </button>
                @if ($existRequest)
                    <button wire:click="proceedingRequest"
                        class="bg-green-500  hover:bg-blue-500 text-white font-bold py-2 px-4 rounded">
                        Proceeding Request
                    </button>
                @endif

            </div>
        </div>
    </div>

    <div class="mt-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <table class="mx-5 text-left w-full border-collapse">
                <thead>
                    <tr>
                        <th
                            class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                            ID</th>
                        <th
                            class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                            Ordered by</th>
                        <th
                            class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                            Ordered At</th>
                        <th
                            class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                            Status</th>
                        <th
                            class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                            Check In</th>
                        <th
                            class="py-4 px-6 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">
                            Check OUT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr wire:key="order-{{ $order->id }}" class="hover:bg-grey-lighter">
                            <td class="py-4 px-6 border-b border-grey-light">{{ $order->id }}</td>
                            <td class="py-4 px-6 border-b border-grey-light">{{ $order->user->name }}</td>
                            <td class="py-4 px-6 border-b border-grey-light">{{ $order?->created_at->diffForHumans() }}</td>
                            <td class="py-4 px-6 border-b border-grey-light">{{ $order->status }}</td>
                            <td class="py-4 px-6 border-b border-grey-light">{{ $order->check_in?->diffForHumans() }}</td>
                            <td class="py-4 px-6 border-b border-grey-light">{{ $order->check_out?->diffForHumans() }}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
            <div class="m-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
    <script>
        const minutes = {{ $totalTimeInMinutes }};
        let time = minutes * 60; //minutes * 60 seconds
        let refreshIntervalId = setInterval(updateCountdown, 1000); //update every 1 second

        function updateCountdown() {
            const minutes = Math.floor(time / 60); // rounds a number DOWN to the nearest integer
            let seconds = time % 60;

            seconds = seconds < 10 ? '0' + seconds : seconds;
            const contdownEl = document.getElementById("countdown");
            contdownEl.innerHTML = `${minutes}:${seconds}`;

            time--;

            if (time < 0) { //stop the setInterval whe time = 0 for avoid negative time
                clearInterval(refreshIntervalId);
            }
        }
    </script>
