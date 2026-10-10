// DDD: pure pace rule. Same 15% bands as the Laravel PaceCalculationService. No Redis, no HTTP.

export function paceStatus(actualSeconds, parSeconds) {
  if (actualSeconds <= parSeconds) {
    return 'on_pace';
  }

  const slowLimit = parSeconds + Math.floor((parSeconds * 15) / 100);

  if (actualSeconds <= slowLimit) {
    return 'slow';
  }

  return 'very_slow';
}

export function isSlow(current, parTime) {
  return paceStatus(current, parTime) !== 'on_pace';
}
