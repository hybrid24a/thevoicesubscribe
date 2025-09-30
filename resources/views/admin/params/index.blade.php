@extends('admin.layout', [
	'title' => 'Paramètres',
])

@section('content')
<div class="container">
  <div class="block">
    <div class="block-header">
      <div class="block-title">Réserver un numéro de facture</div>
      <div class="block-options"></div>
    </div>
    <div class="block-content with-padding">
      <form action="{{ route('params') }}" method="POST" id="reserve-invoice-form">
        @csrf
        <div class="form-content">
          <div class="form-control inline">
            <label for="invoice_number">Prochain numéro de facture disponible : "{{ $availableInvoiceNumber }}", réserver le numéro :</label>
            <div class="input-wrapper">
              <input type="text" name="invoice_number" value="{{ old('invoice_number', $availableInvoiceNumber) }}" />
            </div>
          </div>
          @error('invoice_number')
          <span class="help-block">
            <strong>{{ $errors->first('invoice_number') }}</strong>
          </span>
          @enderror
        </div>
      </form>
    </div>
    <div class="block-footer">
      <div class="side">
        <button class="button" type="submit" form="reserve-invoice-form">Réserver</button>
      </div>
      <div class="side">
      </div>
    </div>
  </div>
  <div class="block" style="margin: 20px 0 0;">
    <div class="block-header">
      <div class="block-title">Liste des numéros réservés</div>
      <div class="block-options"></div>
    </div>
    <div class="block-content with-padding">
      @if ($reservedInvoices->isEmpty())
        <p style="margin: 0;">Aucun numéro de facture réservé.</p>
      @else
      <div class="table-wrapper">
        <table class="full-width">
          <thead>
            <tr>
              <th>Numéro de facture</th>
              <th>Année</th>
              <th>Date de réservation</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($reservedInvoices as $reservedInvoice)
            <tr>
              <td>{{ $reservedInvoice->getNumber() }}</td>
              <td>{{ $reservedInvoice->getYear() }}</td>
              <td>{{ $reservedInvoice->getCreatedAt()->format('d/m/Y H:i') }}</td>
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
