@extends('admin.layout', [
	'title' => 'Commandes',
])

@section('content')
<div class="container">
  <div class="block">
    <div class="block-header">
      <div class="block-title">Liste des commandes</div>
      <div class="block-options"></div>
    </div>
    <div class="block-content with-padding">
      @if ($orders->isEmpty())
        <p style="margin: 0;">Aucune commande trouvée.</p>
      @else
      <div class="table-wrapper">
        <table class="full-width">
          <thead>
            <tr>
              <th>Numéro de commande</th>
              <th>Utilisateur</th>
              <th>Email</th>
              <th>Produits</th>
              <th>Status</th>
              <th>Total</th>
              <th>Don</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($orders as $order)
            <tr>
              <td>{{ $order->getNumber() }}</td>
              <td>{{ $order->getUser()->getName() }}</td>
              <td>{{ $order->getUser()->getEmail() }}</td>
              <td>
                @foreach ($order->getOrderItems() as $item)
                  <div>{{ $item['title'] }}</div>
                @endforeach
              </td>
              <td>{{ $orderStatusesDisplay[$order->getStatus()] }}</td>
              <td>{{ number_format($order->getPrice(), 2) }} MAD</td>
              <td>{{ $order->getTip() ? number_format($order->getTip(), 2) . ' MAD' : '------' }}</td>
              <td>{{ $order->getCreatedAt()->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection

@section('footer')
@endsection
