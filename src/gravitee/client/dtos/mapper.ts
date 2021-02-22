import { ApplicationDTO } from 'src/gravitee/client/dtos/application-unit.dto';
import { SubscriptionUnitDTO } from 'src/gravitee/client/dtos/subscription-unit.dto';
import { ApplicationId } from 'src/gravitee/types/application.type';
import { ApiKey, Key } from 'src/gravitee/types/key.type';
import {
  PlanId,
  Subscription,
  SubscriptionId,
} from 'src/gravitee/types/subscription.type';

export class SubscriptionMapper {
  static dtoToDomain(
    subscription: SubscriptionUnitDTO,
    application: ApplicationDTO,
  ): Subscription {
    return {
      id: (subscription.id as string) as SubscriptionId,
      plan: (subscription.plan as string) as PlanId,
      keys: subscription.keys.map((key) => ({
        id: (key.id as string) as ApiKey,
      })) as Key[],
      application: {
        id: (application.id as string) as ApplicationId,
        name: application.name,
      },
    };
  }
}
