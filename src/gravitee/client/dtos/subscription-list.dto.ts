import { ApplicationDTOId } from 'src/gravitee/client/dtos/application-unit.dto';
import {
  PlanDTOId,
  SubscriptionDTOId,
} from 'src/gravitee/client/dtos/subscription-unit.dto';

export type SubscriptionListUnitDTO = {
  id: SubscriptionDTOId;
  application: ApplicationDTOId;
  plan: PlanDTOId;
};

export type SubscriptionListDTO = [SubscriptionListUnitDTO];
