import { KeyDTO } from 'src/gravitee/client/dtos/key.dto';
import { SubscriptionListUnitDTO } from 'src/gravitee/client/dtos/subscription-list.dto';
import { ApplicationId } from 'src/gravitee/types/application.type';
import { ApiKey, Key } from 'src/gravitee/types/key.type';
import {
  PlanId,
  Subscription,
  SubscriptionId,
} from 'src/gravitee/types/subscription.type';

export class SubscriptionMapper {
  static dtoToDomain(
    keys: [KeyDTO],
    subscription: SubscriptionListUnitDTO,
  ): Subscription {
    return {
      id: (subscription.id as string) as SubscriptionId,
      plan: (subscription.plan.id as string) as PlanId,
      keys: keys.map((key) => ({
        id: (key.key as string) as ApiKey,
      })) as Key[],
      application: {
        id: (subscription.application.id as string) as ApplicationId,
        name: subscription.application.name,
      },
    };
  }
}
