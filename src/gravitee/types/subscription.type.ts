import { Application } from 'src/gravitee/types/application.type';
import { Key } from 'src/gravitee/types/key.type';
import { Brand } from 'src/types';

export type SubscriptionId = Brand<string, 'SubscriptionId'>;
export type PlanId = Brand<string, 'PlanId'>;

export type Subscription = {
  id: SubscriptionId;
  plan: PlanId;
  application: Application;
  keys: Key[];
};
